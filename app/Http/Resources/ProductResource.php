<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ImageResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ShopResource;
use App\Http\Resources\BrandResource;
use App\Http\Resources\CompositionResource;

/**
 * @OA\Schema(
 *     schema="ProductResource",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="price", type="number"),
 *     @OA\Property(property="discount", type="integer"),
 *     @OA\Property(property="description", type="string"),
 *     @OA\Property(property="gender", type="string"),
 *     @OA\Property(property="sizes", type="string"),
 *     @OA\Property(property="separated_sizes", type="string"),
 *     @OA\Property(property="color", type="string"),
 *     @OA\Property(property="manufacturer", type="string"),
 *     @OA\Property(property="width", type="number"),
 *     @OA\Property(property="height", type="number"),
 *     @OA\Property(property="weight", type="number"),
 *     @OA\Property(property="production_time", type="integer"),
 *     @OA\Property(property="min_order", type="integer"),
 *     @OA\Property(property="seller_status", type="boolean"),
 *     @OA\Property(property="status", type="boolean"),
 *     @OA\Property(property="shop_id", type="integer"),
 *     @OA\Property(property="brand_id", type="integer"),
 *     @OA\Property(property="category_id", type="integer"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
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