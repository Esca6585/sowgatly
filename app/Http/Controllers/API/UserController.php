<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Rules\ImageOrBase64;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

/**
 * @OA\Tag(
 *     name="Users",
 *     description="API Endpoints of Users"
 * )
 */
/**
 * @OA\Schema(
 *     schema="UserRequest",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Esen Meredow"),
 *     @OA\Property(property="phone_number", type="integer", example="65656585"),
 *     @OA\Property(
 *         property="image",
 *         type="string",
 *         format="binary",
 *         description="Image file"
 *     ),
 * )
 */
class UserController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/users",
     *     tags={"Users"},
     *     security={{"sanctum":{}}},
     *     summary="Get list of users",
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200", 
     *         description="List of users"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            $users = User::paginate(15);

            return UserResource::collection($users);
        } catch (\Exception $e) {
            // It's generally a good practice to log the error
            \Log::error('Error fetching users: ' . $e->getMessage());
            
            return response()->json(['error' => 'An error occurred while fetching users'], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/users",
     *     tags={"Users"},
     *     security={{"sanctum":{}}},
     *     summary="Create a new user",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="name", type="string", example="Esen Meredow"),
     *                 @OA\Property(property="phone_number", type="integer", example="65656585"),
     *                 @OA\Property(
     *                     property="image",
     *                     type="string",
     *                     format="binary",
     *                     description="Image file"
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="201", 
     *         description="User created",
     *         @OA\JsonContent(ref="#/components/schemas/UserRequest"),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|unique:users,phone_number|min:8',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
        ]);

        $user = User::create($validatedData);

        $this->uploadImage($user, $request->file('image'));

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/users/{id}",
     *     tags={"Users"},
     *     security={{"sanctum":{}}},
     *     summary="Get a user",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response="200", description="User details"),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *     )
     * )
     */
    public function show(User $user)
    {
        return new UserResource($user);
    }

    /**
     * @OA\Post(
     *     path="/api/users/{id}",
     *     tags={"Users"},
     *     security={{"sanctum":{}}},
     *     summary="Update a user",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="name", type="string", example="Esca Meredoff"),
     *                 @OA\Property(
     *                      property="image",
     *                      type="string",
     *                      format="binary",
     *                      description="Image file upload or base64 encoded image string"
     *                 ),
     *                 @OA\Property(property="phone_number", type="string", example="65123456"),
     *                 @OA\Property(property="email", type="string", example="user@example.com"),
     *                 @OA\Property(property="status", type="boolean", example=true),
     *                 @OA\Property(property="_method", type="string", example="PUT"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200", 
     *         description="User updated",
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="User not found"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *     )
     * )
     */
    public function update(Request $request, User $user)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|unique:users,phone_number,' . $user->id,
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'image' => ['sometimes', 'nullable', new ImageOrBase64(['jpeg', 'png', 'jpg', 'gif']), 'max:10240'],
            'status' => 'boolean',
        ]);

        // Update user data
        $user->update($validatedData);

        // Handle image upload or base64 image
        if ($request->has('image')) {
            $this->uploadImage($user, $request->image);
        }

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'user' => $user
        ]);
    }

    /**
     * Upload and save the user image.
     *
     * @param User $user
     * @param \Illuminate\Http\UploadedFile|string|null $image
     * @return void
     */
    private function uploadImage(User $user, $image)
    {
        if ($image) {
            $date = date("d-m-Y H-i-s");
            $fileRandName = Str::random(10);
            $path = 'user/' . Str::slug($user->name . '-' . $date) . '/';

            if ($image instanceof \Illuminate\Http\UploadedFile) {
                $fileExt = $image->getClientOriginalExtension();
                $fileName = $fileRandName . '.' . $fileExt;
                Storage::disk('public')->putFileAs($path, $image, $fileName);
            } elseif (is_string($image) && strpos($image, 'base64') !== false) {
                $fileExt = $this->getBase64Extension($image);
                $fileName = $fileRandName . '.' . $fileExt;
                $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $image));
                Storage::disk('public')->put($path . $fileName, $imageData);
            } else {
                throw new \InvalidArgumentException('Invalid image format');
            }

            $originalImage = $path . $fileName;

            // Delete old image if exists
            if ($user->image && Storage::disk('public')->exists($user->image)) {
                Storage::disk('public')->delete($user->image);
            }

            $user->image = $originalImage;
            $user->save();
        }
    }

    /**
     * Get file extension from base64 string.
     *
     * @param string $base64String
     * @return string
     */
    private function getBase64Extension($base64String)
    {
        $data = explode(',', $base64String);
        $mime = explode(';', $data[0]);
        $mime = explode('/', $mime[0]);
        return $mime[1] ?? 'png'; // Default to png if extension can't be determined
    }

    /**
     * @OA\Delete(
     *     path="/api/users/{id}",
     *     tags={"Users"},
     *     security={{"sanctum":{}}},
     *     summary="Delete a user",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response="204", description="User deleted"),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *     )
     * )
     */
    public function destroy(User $user, $lang = null)
    {
        $this->deleteImage($user);

        $user->delete();

        return response(null, 204);
    }

    /**
     * Delete the image associated with the user.
     *
     * @param User $user
     * @return void
     */
    public function deleteImage(User $user)
    {
        if ($user->image) {
            $imagePath = public_path($user->image);
            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
        }
    }
}