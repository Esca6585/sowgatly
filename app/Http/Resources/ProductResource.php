<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ImageResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ShopResource;
use App\Http\Resources\BrandResource;
use App\Http\Resources\CompositionResource;

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
            'discountedPrice' => $this->getDiscountedPrice(),
            'attributes' => $this->attributes,
            'code' => $this->code,
            'category_id' => $this->category_id,
            'shop_id' => $this->shop_id,
            'brand_id' => $this->brand_id,
            'status' => $this->status,
            'gender' => $this->gender,
            'sizes' => $this->sizes,
            'separated_sizes' => $this->separated_sizes,
            'color' => $this->color,
            'manufacturer' => $this->manufacturer,
            'width' => $this->width,
            'height' => $this->height,
            'weight' => $this->weight,
            'production_time' => $this->production_time,
            'min_order' => $this->min_order,
            'seller_status' => $this->seller_status,
            'images' => ImageResource::collection($this->whenLoaded('images')),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'shop' => new ShopResource($this->whenLoaded('shop')),
            'brand' => new BrandResource($this->whenLoaded('brand')),
            'compositions' => CompositionResource::collection($this->whenLoaded('compositions')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}