<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\Address;
use App\Models\Region;
use Illuminate\Http\Request;
use App\Http\Resources\ShopResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

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
 *     @OA\Property(property="email", type="string", example="modahouse@modahouse.top"),
 *     @OA\Property(property="mon_fri_open", type="string", example="09:00"),
 *     @OA\Property(property="mon_fri_close", type="string", example="18:00"),
 *     @OA\Property(property="sat_sun_open", type="string", example="10:00"),
 *     @OA\Property(property="sat_sun_close", type="string", example="16:00"),
 *     @OA\Property(property="image", type="string", example="shop_images/abcdef1234.jpg"),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="region_id", type="integer", example=1),
 *     @OA\Property(
 *         property="address",
 *         type="object",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="address_name", type="string", example="123 Main St"),
 *         @OA\Property(property="postal_code", type="string", example="744000"),
 *     ),
 *     @OA\Property(
 *         property="region",
 *         type="object",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="New York"),
 *         @OA\Property(property="type", type="string", example="city"),
 *         @OA\Property(property="parent_id", type="integer", example=null),
 *     ),
 * )
 */
class ShopController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/shops",
     *     tags={"Shops"},
     *     security={{"sanctum":{}}},
     *     summary="Get list of shops",
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200", 
     *         description="List of shops",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/ShopResource")
     *             ),
     *             @OA\Property(property="links", type="object"),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *     )
     * )
     */
    public function index()
    {
        $shops = Shop::with(['address', 'region'])->paginate(10);
        return ShopResource::collection($shops);
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
     *               @OA\Property(property="email", type="string", example="modahouse@modahouse.top"),
     *               @OA\Property(property="mon_fri_open", type="string", example="09:00"),
     *               @OA\Property(property="mon_fri_close", type="string", example="18:00"),
     *               @OA\Property(property="sat_sun_open", type="string", example="10:00"),
     *               @OA\Property(property="sat_sun_close", type="string", example="16:00"),
     *               @OA\Property(property="image", type="string", format="binary"),
     *               @OA\Property(property="region_id", type="integer", example=1),
     *               @OA\Property(property="address_name", type="string", example="123 Main St"),
     *               @OA\Property(property="postal_code", type="string", example="744000"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="201", 
     *         description="Shop created",
     *         @OA\JsonContent(ref="#/components/schemas/ShopResource")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:shops,email',
            'mon_fri_open' => 'required|date_format:H:i',
            'mon_fri_close' => 'required|date_format:H:i|after:mon_fri_open',
            'sat_sun_open' => 'required|date_format:H:i',
            'sat_sun_close' => 'required|date_format:H:i|after:sat_sun_open',
            'image' => 'nullable|image|max:2048',
            'region_id' => 'required|exists:regions,id',
            'address_name' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
        ]);

        DB::beginTransaction();

        try {
            $shop = Shop::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'mon_fri_open' => $validatedData['mon_fri_open'],
                'mon_fri_close' => $validatedData['mon_fri_close'],
                'sat_sun_open' => $validatedData['sat_sun_open'],
                'sat_sun_close' => $validatedData['sat_sun_close'],
                'region_id' => $validatedData['region_id'],
                'user_id' => Auth::id(),
            ]);

            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('shop_images', 'public');
                $shop->image = $imagePath;
                $shop->save();
            }

            $shop->address()->create([
                'address_name' => $validatedData['address_name'],
                'postal_code' => $validatedData['postal_code'],
            ]);

            DB::commit();

            return new ShopResource($shop->load('address', 'region'));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'An error occurred while creating the shop'], 500);
        }
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
     *     @OA\Response(
     *         response="200", 
     *         description="Shop details",
     *         @OA\JsonContent(ref="#/components/schemas/ShopResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Shop not found",
     *     )
     * )
     */
    public function show(Shop $shop)
    {
        return new ShopResource($shop->load('address', 'region'));
    }

    /**
     * @OA\Put(
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
     *               @OA\Property(property="name", type="string", example="Updated Modahouse"),
     *               @OA\Property(property="email", type="string", example="updated@example.com"),
     *               @OA\Property(property="mon_fri_open", type="string", example="08:00"),
     *               @OA\Property(property="mon_fri_close", type="string", example="19:00"),
     *               @OA\Property(property="sat_sun_open", type="string", example="09:00"),
     *               @OA\Property(property="sat_sun_close", type="string", example="17:00"),
     *               @OA\Property(property="image", type="string", format="binary"),
     *               @OA\Property(property="region_id", type="integer", example=2),
     *               @OA\Property(property="address_name", type="string", example="456 New St"),
     *               @OA\Property(property="postal_code", type="string", example="744000"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200", 
     *         description="Shop updated",
     *         @OA\JsonContent(ref="#/components/schemas/ShopResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Shop not found",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *     )
     * )
     */
    public function update(Request $request, Shop $shop)
    {
        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|nullable|email|unique:shops,email,' . $shop->id,
            'mon_fri_open' => 'sometimes|required|date_format:H:i',
            'mon_fri_close' => 'sometimes|required|date_format:H:i|after:mon_fri_open',
            'sat_sun_open' => 'sometimes|required|date_format:H:i',
            'sat_sun_close' => 'sometimes|required|date_format:H:i|after:sat_sun_open',
            'image' => 'nullable|image|max:2048',
            'region_id' => 'sometimes|required|exists:regions,id',
            'address_name' => 'sometimes|required|string|max:255',
            'postal_code' => 'sometimes|required|string|max:20',
        ]);

        DB::beginTransaction();

        try {
            $shop->update($validatedData);

            if ($request->hasFile('image')) {
                if ($shop->image) {
                    Storage::disk('public')->delete($shop->image);
                }
                $imagePath = $request->file('image')->store('shop_images', 'public');
                $shop->image = $imagePath;
                $shop->save();
            }

            if ($shop->address) {
                $shop->address->update([
                    'address_name' => $validatedData['address_name'] ?? $shop->address->address_name,
                    'postal_code' => $validatedData['postal_code'] ?? $shop->address->postal_code,
                ]);
            } else {
                $shop->address()->create([
                    'address_name' => $validatedData['address_name'],
                    'postal_code' => $validatedData['postal_code'],
                ]);
            }

            DB::commit();

            return new ShopResource($shop->load('address', 'region'));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'An error occurred while updating the shop'], 500);
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
     *     @OA\Response(
     *         response="204",
     *         description="Shop deleted"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Shop not found",
     *     )
     * )
     */
    public function destroy(Shop $shop)
    {
        if ($shop->image) {
            Storage::disk('public')->delete($shop->image);
        }
        $shop->delete();
        return response()->json(null, 204);
    }
}