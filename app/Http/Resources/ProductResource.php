<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ImageResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ShopResource;

class ProductResource extends JsonResource
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
            'description' => $this->description,
            'price' => $this->price,
            'discount' => $this->discount,
            'discountPrice' => $this->getDiscountPrice(),
            'attributes' => $this->attributes,
            'code' => $this->code,
            'category_id' => $this->category_id,
            'status' => $this->status,
            'images' => ImageResource::collection($this->whenLoaded('images')),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'shop' => new ShopResource($this->whenLoaded('shop')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}