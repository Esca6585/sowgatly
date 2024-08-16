<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Http\Resources\AddressResource;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Addresses",
 *     description="API Endpoints of Addresses"
 * )
 */
class AddressController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/addresses",
     *     security={{"sanctum":{}}},
     *     summary="Get a list of addresses",
     *     tags={"Addresses"},
     *     @OA\Response(response="200", description="Successful operation")
     * )
     */
    public function index()
    {
        return AddressResource::collection(Address::all());
    }

    /**
     * @OA\Post(
     *     path="/api/addresses",
     *     security={{"sanctum":{}}},
     *     summary="Create a new address",
     *     tags={"Addresses"},
     *     @OA\RequestBody(
     *         @OA\JsonContent(ref="#/components/schemas/AddressRequest")
     *     ),
     *     @OA\Response(response="201", description="Address created successfully")
     * )
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'street' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
        ]);

        $address = Address::create($validatedData);

        return new AddressResource($address);
    }

    /**
     * @OA\Get(
     *     path="/api/addresses/{id}",
     *     security={{"sanctum":{}}},
     *     summary="Get a specific address",
     *     tags={"Addresses"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response="200", description="Successful operation"),
     *     @OA\Response(response="404", description="Address not found")
     * )
     */
    public function show(Address $address)
    {
        return new AddressResource($address);
    }

    /**
     * @OA\Put(
     *     path="/api/addresses/{id}",
     *     security={{"sanctum":{}}},
     *     summary="Update an existing address",
     *     tags={"Addresses"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(ref="#/components/schemas/AddressRequest")
     *     ),
     *     @OA\Response(response="200", description="Address updated successfully"),
     *     @OA\Response(response="404", description="Address not found")
     * )
     */
    public function update(Request $request, Address $address)
    {
        $validatedData = $request->validate([
            'street' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
        ]);

        $address->update($validatedData);

        return new AddressResource($address);
    }

    /**
     * @OA\Delete(
     *     path="/api/addresses/{id}",
     *     security={{"sanctum":{}}},
     *     summary="Delete an address",
     *     tags={"Addresses"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response="204", description="Address deleted successfully"),
     *     @OA\Response(response="404", description="Address not found")
     * )
     */
    public function destroy(Address $address)
    {
        $address->delete();

        return response()->noContent();
    }
}