<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use App\Models\Shop;
use Illuminate\Http\Request;
use App\Http\Resources\ShopResource;
use Str;
use Auth;
use Storage;

/**
 * @OA\Tag(
 *     name="Shops",
 *     description="API Endpoints of shops"
 * )
 */
/**
 * @OA\Schema(
 *     schema="ShopResource",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Modahouse"),
 *     @OA\Property(property="email", type="string", example="esca6585@gmail.com"),
 *     @OA\Property(
 *         property="image",
 *         type="string",
 *         format="binary",
 *         description="Image file"
 *     ),
 *     @OA\Property(property="address", type="integer", example="Oguzhan 123"),
 *     @OA\Property(property="mon_fri_open", type="integer", example="09:00"),
 *     @OA\Property(property="mon_fri_close", type="integer", example="18:00"),
 *     @OA\Property(property="sat_sun_open", type="integer", example="09:00"),
 *     @OA\Property(property="sat_sun_close", type="integer", example="13:00"),
 *     @OA\Property(property="user_id", type="integer", example=1),
 * )
 */
class ShopController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/shops",
     *     tags={"Shops"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     summary="Get list of shops",
     *     @OA\Response(response="200", description="List of shops"),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            $shops = Shop::with('user')->paginate(15);
            return ShopResource::collection($shops);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while fetching shops'], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/shops",
     *     tags={"Shops"},
     *     security={{"sanctum":{}}},
     *     summary="Create a new shop",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *               @OA\Property(property="name", type="string", example="Modahouse"),
     *               @OA\Property(property="email", type="string", example="esca6585@gmail.com"),
     *               @OA\Property(
     *                  property="image",
     *                  type="string",
     *                  format="binary",
     *                  description="Image file"
     *               ),
     *               @OA\Property(property="address", type="integer", example="Oguzhan 123"),
     *               @OA\Property(property="mon_fri_open", type="integer", example="09:00"),
     *               @OA\Property(property="mon_fri_close", type="integer", example="18:00"),
     *               @OA\Property(property="sat_sun_open", type="integer", example="09:00"),
     *               @OA\Property(property="sat_sun_close", type="integer", example="13:00"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="201", 
     *         description="Shop created",
     *         @OA\JsonContent(ref="#/components/schemas/ShopResource")
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
            'email' => 'required|string|email|max:255|unique:shops,email',
            'image' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'address' => 'required|string|max:255',
            'mon_fri_open' => 'required|date_format:H:i',
            'mon_fri_close' => 'required|date_format:H:i|after:mon_fri_open',
            'sat_sun_open' => 'required|date_format:H:i',
            'sat_sun_close' => 'required|date_format:H:i|after:sat_sun_open',
        ]);

        // Get the authenticated user's ID from the token
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        // Add the user_id to the validated data
        $validatedData['user_id'] = $user->id;

        $shop = Shop::create($validatedData);

        $this->uploadImage($shop, $request->file('image'));

        return response()->json([
            'success' => true,
            'message' => 'Shop created successfully',
            'shop' => $shop,
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/shops/{id}",
     *     tags={"Shops"},
     *     security={{"sanctum":{}}},
     *     summary="Get a shop",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response="200", description="Shop details"),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *     )
     * )
     */
    public function show(Shop $shop)
    {
        return new ShopResource($shop);
    }

    /**
     * @OA\Put(
     *     path="/api/shops/{shop}",
     *     tags={"Shops"},
     *     security={{"sanctum":{}}},
     *     summary="Update a shop",
     *     @OA\Parameter(
     *         name="shop",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *               @OA\Property(property="name", type="string", example="Modahouse"),
     *               @OA\Property(property="email", type="string", example="esca6585@gmail.com"),
     *               @OA\Property(
     *                  property="image",
     *                  type="string",
     *                  format="binary",
     *                  description="Image file"
     *               ),
     *               @OA\Property(property="address", type="string", example="Oguzhan 123"),
     *               @OA\Property(property="mon_fri_open", type="string", example="09:00"),
     *               @OA\Property(property="mon_fri_close", type="string", example="18:00"),
     *               @OA\Property(property="sat_sun_open", type="string", example="09:00"),
     *               @OA\Property(property="sat_sun_close", type="string", example="13:00"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200", 
     *         description="Shop updated",
     *         @OA\JsonContent(ref="#/components/schemas/ShopResource")
     *     ),
     *     @OA\Response(
     *         response="403",
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Shop not found"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *     )
     * )
     */
    public function update(Request $request, Shop $shop)
    {
        $user = Auth::user();

        if ($shop->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to update this shop',
            ], 403);
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:shops,email,' . $shop->id,
            'image' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'address' => 'required|string|max:255',
            'mon_fri_open' => 'required|date_format:H:i',
            'mon_fri_close' => 'required|date_format:H:i|after:mon_fri_open',
            'sat_sun_open' => 'required|date_format:H:i',
            'sat_sun_close' => 'required|date_format:H:i|after:sat_sun_open',
        ]);

        // Update shop data
        $shop->update($validatedData);

        // Handle image upload
        if ($request->hasFile('image')) {
            $this->uploadImage($shop, $request->file('image'));
        }

        return new ShopResource($shop);
    }
    // /**
    //  * @OA\Post(
    //  *     path="/api/shops/{id}",
    //  *     tags={"Shops"},
    //  *     security={{"sanctum":{}}},
    //  *     summary="Update a shop",
    //  *     @OA\Parameter(
    //  *         name="id",
    //  *         in="path",
    //  *         required=true,
    //  *         @OA\Schema(type="integer")
    //  *     ),
    //  *     @OA\RequestBody(
    //  *         @OA\MediaType(
    //  *             mediaType="multipart/form-data",
    //  *             @OA\Schema(
    //  *               @OA\Property(property="name", type="string", example="Modahouse"),
    //  *               @OA\Property(property="email", type="string", example="esca6585@gmail.com"),
    //  *               @OA\Property(
    //  *                  property="image",
    //  *                  type="string",
    //  *                  format="binary",
    //  *                  description="Image file"
    //  *               ),
    //  *               @OA\Property(property="address", type="string", example="Oguzhan 123"),
    //  *               @OA\Property(property="mon_fri_open", type="string", example="09:00"),
    //  *               @OA\Property(property="mon_fri_close", type="string", example="18:00"),
    //  *               @OA\Property(property="sat_sun_open", type="string", example="09:00"),
    //  *               @OA\Property(property="sat_sun_close", type="string", example="13:00"),
    //  *               @OA\Property(property="_method", type="string", example="PUT"),
    //  *             )
    //  *         )
    //  *     ),
    //  *     @OA\Response(
    //  *         response="200", 
    //  *         description="Shop updated",
    //  *     ),
    //  *     @OA\Response(
    //  *         response="403",
    //  *         description="Forbidden"
    //  *     ),
    //  *     @OA\Response(
    //  *         response="404",
    //  *         description="Shop not found"
    //  *     ),
    //  *     @OA\Response(
    //  *         response=401,
    //  *         description="Unauthenticated",
    //  *     )
    //  * )
    //  */
    // public function update(Request $request, $id)
    // {
    //     $user = Auth::user();
    
    //     if (!$user) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Unauthenticated',
    //         ], 401);
    //     }
    
    //     $shop = Shop::where('id', $id)->where('user_id', $user->id)->first();
    
    //     if (!$shop) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Shop not found or you do not have permission to update this shop',
    //         ], 403);
    //     }
    
    //     $validatedData = $request->validate([
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|string|email|max:255|unique:shops,email,' . $shop->id,
    //         'image' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
    //         'address' => 'required|string|max:255',
    //         'mon_fri_open' => 'required|date_format:H:i',
    //         'mon_fri_close' => 'required|date_format:H:i|after:mon_fri_open',
    //         'sat_sun_open' => 'required|date_format:H:i',
    //         'sat_sun_close' => 'required|date_format:H:i|after:sat_sun_open',
    //     ]);
    
    //     // Update shop data
    //     $shop->update($validatedData);
    
    //     // Handle image upload
    //     if ($request->hasFile('image')) {
    //         $this->uploadImage($shop, $request->file('image'));
    //     }
    
    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Shop updated successfully',
    //         'shop' => $shop,
    //     ]);
    // }
    // /**
    //  * @OA\Post(
    //  *     path="/api/shops/{id}",
    //  *     tags={"Shops"},
    //  *     security={{"sanctum":{}}},
    //  *     summary="Update a shop",
    //  *     @OA\Parameter(
    //  *         name="id",
    //  *         in="path",
    //  *         required=true,
    //  *         @OA\Schema(type="integer")
    //  *     ),
    //  *     @OA\RequestBody(
    //  *         @OA\MediaType(
    //  *             mediaType="multipart/form-data",
    //  *             @OA\Schema(
    //  *               @OA\Property(property="id", type="integer", example=1),
    //  *               @OA\Property(property="name", type="string", example="Modahouse"),
    //  *               @OA\Property(property="email", type="string", example="esca6585@gmail.com"),
    //  *               @OA\Property(
    //  *                  property="image",
    //  *                  type="string",
    //  *                  format="binary",
    //  *                  description="Image file"
    //  *               ),
    //  *               @OA\Property(property="address", type="integer", example="Oguzhan 123"),
    //  *               @OA\Property(property="mon_fri_open", type="integer", example="09:00"),
    //  *               @OA\Property(property="mon_fri_close", type="integer", example="18:00"),
    //  *               @OA\Property(property="sat_sun_open", type="integer", example="09:00"),
    //  *               @OA\Property(property="sat_sun_close", type="integer", example="13:00"),
    //  *               @OA\Property(property="_method", type="string", example="PUT"),
    //  *             )
    //  *         )
    //  *     ),
    //  *     @OA\Response(
    //  *         response="200", 
    //  *         description="Shop updated",
    //  *     ),
    //  *     @OA\Response(
    //  *         response="404",
    //  *         description="Shop not found"
    //  *     ),
    //  *     @OA\Response(
    //  *         response=401,
    //  *         description="Unauthenticated",
    //  *     )
    //  * )
    //  */
    // public function update(Request $request, Shop $shop)
    // {
    //     $validatedData = $request->validate([
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|string|email|max:255|unique:shops,email',
    //         'image' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
    //         'address' => 'required|string|max:255',
    //         'mon_fri_open' => 'required|date_format:H:i',
    //         'mon_fri_close' => 'required|date_format:H:i|after:mon_fri_open',
    //         'sat_sun_open' => 'required|date_format:H:i',
    //         'sat_sun_close' => 'required|date_format:H:i|after:sat_sun_open',
    //     ]);

    //     // Update shop data
    //     $shop->update($validatedData);

    //     // Handle image upload
    //     if ($request->hasFile('image')) {
    //         $this->uploadImage($shop, $request->file('image'));
    //     }

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Shop updated successfully',
    //     ]);
    // }

    /**
     * Upload and save the shop image.
     *
     * @param Shop $shop
     * @param \Illuminate\Http\UploadedFile|null $image
     * @return void
     */
    protected function uploadImage(Shop $shop, $image)
    {
        if ($image) {
            // Delete the old image first
            $this->deleteImage($shop);

            $date = date("d-m-Y H-i-s");
            $fileRandName = Str::random(10);
            $fileExt = $image->getClientOriginalExtension();

            $fileName = $fileRandName . '.' . $fileExt;
            
            $path = 'shop/' . Str::slug($shop->name . '-' . $date) . '/';

            // Ensure the directory exists
            Storage::disk('public')->makeDirectory($path);

            // Store the file
            $image->storeAs('public/' . $path, $fileName);
            
            $originalImage = $path . $fileName;

            $shop->image = $originalImage;
            $shop->save();
        }
    }

    protected function deleteImage(Shop $shop)
    {
        if ($shop->image) {
            Storage::disk('public')->delete($shop->image);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/shops/{id}",
     *     tags={"Shops"},
     *     security={{"sanctum":{}}},
     *     summary="Delete a shop",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response="204", description="Shop deleted"),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Shop not found",
     *     )
     * )
     */
    public function destroy(Shop $shop, $lang = null)
    {
        $this->deleteImage($shop);

        $shop->delete();

        return response(null, 204);
    }
}