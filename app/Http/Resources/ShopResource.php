<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="ShopResource",
 *     description="Shop resource",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="email", type="string"),
 *     @OA\Property(property="mon_fri_open", type="string"),
 *     @OA\Property(property="mon_fri_close", type="string"),
 *     @OA\Property(property="sat_sun_open", type="string"),
 *     @OA\Property(property="sat_sun_close", type="string"),
 *     @OA\Property(property="image", type="string"),
 *     @OA\Property(property="user_id", type="integer"),
 *     @OA\Property(property="region_id", type="integer"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(property="address_name", type="string"),
 *     @OA\Property(property="postal_code", type="string")
 * )
 */
class ShopResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'mon_fri_open' => $this->mon_fri_open,
            'mon_fri_close' => $this->mon_fri_close,
            'sat_sun_open' => $this->sat_sun_open,
            'sat_sun_close' => $this->sat_sun_close,
            'image' => $this->image,
            'user_id' => $this->user_id,
            'region_id' => $this->region_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'address_name' => $this->address_name,
            'postal_code' => $this->postal_code,
        ];
    }
}