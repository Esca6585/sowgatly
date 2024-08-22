<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     *
     * @OA\Schema(
     *     schema="AddressResource",
     *     type="object",
     *     title="Address Resource",
     *     @OA\Property(property="street", type="string"),
     *     @OA\Property(property="city", type="string"),
     *     @OA\Property(property="state", type="string"),
     *     @OA\Property(property="country", type="string"),
     *     @OA\Property(property="postal_code", type="string")
     * )
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'shop_id' => $this->shop_id,
            'address_name' => $this->address_name,
            'postal_code' => $this->postal_code,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}