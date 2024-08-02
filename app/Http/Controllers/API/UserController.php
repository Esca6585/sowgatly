<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Str;

/**
 * @OA\Tag(
 *     name="Users",
 *     description="API Endpoints of Users"
 * )
 */
/**
 * @OA\Schema(
 *     schema="UserResource",
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
     *         @OA\JsonContent(ref="#/components/schemas/UserResource"),
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
     *                     property="image",
     *                     type="string",
     *                     format="binary",
     *                     description="Image file"
     *                 ),
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
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
        ]);

        // Update user data
        $user->update($validatedData);

        // Handle image upload
        if ($request->hasFile('image')) {
            $this->uploadImage($user, $request->file('image'));
        }

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
        ]);
    }

    /**
     * Upload and save the user image.
     *
     * @param User $user
     * @param \Illuminate\Http\UploadedFile|null $image
     * @return void
     */
    protected function uploadImage(User $user, $image)
    {
        if ($image) {
            $date = date("d-m-Y H-i-s");
            $fileRandName = Str::random(10);
            $fileExt = $image->getClientOriginalExtension();

            $fileName = $fileRandName . '.' . $fileExt;
            
            $path = 'user/' . Str::slug($user->name . '-' . $date ) . '/';

            $image->move(public_path($path), $fileName);
            
            $originalImage = $path . $fileName;

            // Delete old image if exists
            if ($user->image && file_exists(public_path($user->image))) {
                unlink(public_path($user->image));
            }

            $user->image = $originalImage;
            $user->save();
        }
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
     *     )
     * )
     */
    public function destroy($lang, User $user)
    {
        $this->deleteFolder($user);

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully',
        ]);
    }

    public function deleteImage($user)
    {
        if ($user->image) {
            $imagePath = public_path($user->image);
            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
        }
    }
}