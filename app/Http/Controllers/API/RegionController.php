<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Region;
use App\Http\Resources\RegionResource;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Regions",
 *     description="API Endpoints of Regions"
 * )
 */
class RegionController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/regions",
     *     summary="List all regions",
     *     tags={"Regions"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="type", type="string", enum={"country", "province", "city", "village"}),
     *                 @OA\Property(property="parent_id", type="integer", nullable=true),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        $regions = Region::all();
        return RegionResource::collection($regions);
    }

    /**
     * @OA\Post(
     *     path="/api/regions",
     *     summary="Create a new region",
     *     tags={"Regions"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "type"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="type", type="string", enum={"country", "province", "city", "village"}),
     *             @OA\Property(property="parent_id", type="integer", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="type", type="string", enum={"country", "province", "city", "village"}),
     *             @OA\Property(property="parent_id", type="integer", nullable=true),
     *             @OA\Property(property="created_at", type="string", format="date-time"),
     *             @OA\Property(property="updated_at", type="string", format="date-time")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:country,province,city,village',
            'parent_id' => 'nullable|exists:regions,id',
        ]);

        $region = Region::create($validatedData);

        return new RegionResource($region);
    }

    /**
     * @OA\Get(
     *     path="/api/regions/{id}",
     *     summary="Get a specific region",
     *     tags={"Regions"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="type", type="string", enum={"country", "province", "city", "village"}),
     *             @OA\Property(property="parent_id", type="integer", nullable=true),
     *             @OA\Property(property="created_at", type="string", format="date-time"),
     *             @OA\Property(property="updated_at", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Region not found"
     *     )
     * )
     */
    public function show($id)
    {
        $region = Region::findOrFail($id);
        return new RegionResource($region);
    }

    /**
     * @OA\Put(
     *     path="/api/regions/{id}",
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
     *         @OA\JsonContent(
     *             required={"name", "type"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="type", type="string", enum={"country", "province", "city", "village"}),
     *             @OA\Property(property="parent_id", type="integer", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Region updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="type", type="string", enum={"country", "province", "city", "village"}),
     *             @OA\Property(property="parent_id", type="integer", nullable=true),
     *             @OA\Property(property="created_at", type="string", format="date-time"),
     *             @OA\Property(property="updated_at", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Region not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $region = Region::findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:country,province,city,village',
            'parent_id' => 'nullable|exists:regions,id',
        ]);

        $region->update($validatedData);
        return new RegionResource($region);
    }

    /**
     * @OA\Delete(
     *     path="/api/regions/{id}",
     *     summary="Delete a region",
     *     tags={"Regions"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Region deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Region not found"
     *     )
     * )
     */
    public function destroy($id)
    {
        $region = Region::findOrFail($id);
        $region->delete();
        return response()->noContent();
    }
}