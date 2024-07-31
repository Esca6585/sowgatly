<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use Illuminate\Http\Request;
use App\Http\Resources\ShopResource;
use Str;

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
     *     @OA\Response(response="200", description="List of shops")
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
     *               @OA\Property(property="user_id", type="integer", example=1),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="201", 
     *         description="Shop created",
     *         @OA\JsonContent(ref="#/components/schemas/ShopResource")
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:shops,email',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'address' => 'required|string|max:255',
            'mon_fri_open' => 'required|date_format:H:i',
            'mon_fri_close' => 'required|date_format:H:i|after:mon_fri_open',
            'sat_sun_open' => 'required|date_format:H:i',
            'sat_sun_close' => 'required|date_format:H:i|after:sat_sun_open',
            'user_id' => 'required|exists:users,id',
        ]);

        $shop = Shop::create($validatedData);

        $this->uploadImage($shop, $request->file('image'));

        return response()->json([
            'success' => true,
            'message' => 'Shop created successfully',
        ]);
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
     *     @OA\Response(response="200", description="Shop details")
     * )
     */
    public function show(Shop $shop)
    {
        return new ShopResource($shop);
    }

    /**
     * @OA\Post(
     *     path="/api/shops/{id}",
     *     tags={"Shops"},
     *     security={{"sanctum":{}}},
     *     summary="Update a shop",
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
     *               @OA\Property(property="id", type="integer", example=1),
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
     *               @OA\Property(property="user_id", type="integer", example=1),
     *               @OA\Property(property="_method", type="string", example="PUT"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200", 
     *         description="Shop updated",
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Shop not found"
     *     )
     * )
     */
    public function update(Request $request, Shop $shop)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:shops,email',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'address' => 'required|string|max:255',
            'mon_fri_open' => 'required|date_format:H:i',
            'mon_fri_close' => 'required|date_format:H:i|after:mon_fri_open',
            'sat_sun_open' => 'required|date_format:H:i',
            'sat_sun_close' => 'required|date_format:H:i|after:sat_sun_open',
            'user_id' => 'required|exists:users,id',
        ]);

        // Update shop data
        $shop->update($validatedData);

        // Handle image upload
        if ($request->hasFile('image')) {
            $this->uploadImage($shop, $request->file('image'));
        }

        return response()->json([
            'success' => true,
            'message' => 'Shop updated successfully',
        ]);
    }

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
            $this->deleteFolder($image);

            $date = date("d-m-Y H-i-s");
            $fileRandName = Str::random(10);
            $fileExt = $image->getClientOriginalExtension();

            $fileName = $fileRandName . '.' . $fileExt;
            
            $path = 'shop/' . Str::slug($shop->name . '-' . $date ) . '/';

            $image->move(public_path($path), $fileName);
            
            $originalImage = $path . $fileName;

            $shop->image = $originalImage;
            $shop->save();
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
     *     @OA\Response(response="204", description="Shop deleted")
     * )
     */
    public function destroy(Shop $shop, $lang = null)
    {
        $this->deleteFolder($shop);

        $shop->delete();

        return response()->json([
            'success' => true,
            'message' => 'Shop deleted successfully',
        ], 204);
    }

    public function deleteImage($shop)
    {
        if ($shop->image) {
            $imagePath = public_path($shop->image);
            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
        }
    }
}