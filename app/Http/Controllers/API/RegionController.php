<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Region;
use App\Models\Address;
use App\Http\Resources\RegionResource;
use Illuminate\Http\Request;

/**
 * @OA\Schema(
 *     schema="RegionRequest",
 *     required={"name", "type"},
 *     @OA\Property(property="name", type="string", description="The name of the region"),
 *     @OA\Property(
 *         property="type",
 *         type="string",
 *         enum={"country", "province", "city", "village"},
 *         description="The type of the region"
 *     ),
 *     @OA\Property(
 *         property="parent_id",
 *         type="integer",
 *         nullable=true,
 *         description="The ID of the parent region (if any)"
 *     ),
 *     @OA\Property(
 *         property="address",
 *         type="object",
 *         nullable=true,
 *         description="The address details of the region",
 *         @OA\Property(property="address_name", type="string", nullable=true),
 *         @OA\Property(property="postal_code", type="string", nullable=true)
 *     )
 * )
 */
class RegionController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/regions",
     *     security={{"sanctum":{}}},
     *     summary="Get all regions",
     *     tags={"Regions"},
     *     @OA\Response(response="200", description="Successful operation")
     * )
     */
    public function index()
    {
        $regions = Region::with('address')->get();
        return RegionResource::collection($regions);
    }

    /**
     * @OA\Post(
     *     path="/api/regions",
     *     security={{"sanctum":{}}},
     *     summary="Create a new region",
     *     tags={"Regions"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/RegionRequest")
     *     ),
     *     @OA\Response(response="201", description="Region created successfully")
     * )
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:country,province,city,village',
            'parent_id' => 'nullable|exists:regions,id',
            'address' => 'nullable|array',
            'address.address_name' => 'nullable|string',
            'address.postal_code' => 'nullable|string',
        ]);

        $region = Region::create($validatedData);

        if (isset($validatedData['address'])) {
            $address = new Address($validatedData['address']);
            $region->address()->save($address);
        }

        return new RegionResource($region);
    }

    /**
     * @OA\Get(
     *     path="/api/regions/{id}",
     *     security={{"sanctum":{}}},
     *     summary="Get a specific region",
     *     tags={"Regions"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response="200", description="Successful operation"),
     *     @OA\Response(response="404", description="Region not found")
     * )
     */
    public function show($id)
    {
        $region = Region::with('address')->findOrFail($id);
        return new RegionResource($region);
    }

    /**
     * @OA\Put(
     *     path="/api/regions/{id}",
     *     security={{"sanctum":{}}},
     *     summary="Update a region",
     *     tags={"Regions"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/RegionRequest")
     *     ),
     *     @OA\Response(response="200", description="Region updated successfully"),
     *     @OA\Response(response="404", description="Region not found")
     * )
     */
    public function update(Request $request, $id)
    {
        $region = Region::findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:country,province,city,village',
            'parent_id' => 'nullable|exists:regions,id',
            'address' => 'nullable|array',
            'address.address_name' => 'nullable|string',
            'address.postal_code' => 'nullable|string',
        ]);

        $region->update($validatedData);

        if (isset($validatedData['address'])) {
            if ($region->address) {
                $region->address->update($validatedData['address']);
            } else {
                $address = new Address($validatedData['address']);
                $region->address()->save($address);
            }
        }

        return new RegionResource($region);
    }

    /**
     * @OA\Delete(
     *     path="/api/regions/{id}",
     *     security={{"sanctum":{}}},
     *     summary="Delete a region",
     *     tags={"Regions"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response="204", description="Region deleted successfully"),
     *     @OA\Response(response="404", description="Region not found")
     * )
     */
    public function destroy($id)
    {
        $region = Region::findOrFail($id);
        $region->delete();

        return response()->noContent();
    }
}