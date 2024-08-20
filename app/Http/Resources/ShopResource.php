<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

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
            'image' => $this->image,
            'mon_fri_open' => $this->mon_fri_open,
            'mon_fri_close' => $this->mon_fri_close,
            'sat_sun_open' => $this->sat_sun_open,
            'sat_sun_close' => $this->sat_sun_close,
            'user_id' => $this->user_id,
            'region_id' => $this->region_id,
            'address_id' => $this->address_id,
            'user' => new UserResource($this->whenLoaded('user')),
            'address' => new AddressResource($this->whenLoaded('address')),
            'region' => new RegionResource($this->whenLoaded('region')),
        ];
    }
}