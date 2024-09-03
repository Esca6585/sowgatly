<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="Product",
 *     title="Product",
 *     description="Product model",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="price", type="number", format="float"),
 *     @OA\Property(property="discount", type="integer", nullable=true),
 *     @OA\Property(property="description", type="string"),
 *     @OA\Property(property="gender", type="string", nullable=true),
 *     @OA\Property(property="sizes", type="array", @OA\Items(type="string"), nullable=true),
 *     @OA\Property(property="separated_sizes", type="array", @OA\Items(type="string"), nullable=true),
 *     @OA\Property(property="color", type="string", nullable=true),
 *     @OA\Property(property="manufacturer", type="string", nullable=true),
 *     @OA\Property(property="width", type="number", format="float", nullable=true),
 *     @OA\Property(property="height", type="number", format="float", nullable=true),
 *     @OA\Property(property="weight", type="number", format="float", nullable=true),
 *     @OA\Property(property="production_time", type="integer", nullable=true),
 *     @OA\Property(property="min_order", type="integer", nullable=true),
 *     @OA\Property(property="seller_status", type="boolean"),
 *     @OA\Property(property="status", type="boolean"),
 *     @OA\Property(property="shop_id", type="integer"),
 *     @OA\Property(property="category_id", type="integer"),
 *     @OA\Property(property="brand_ids", type="array", @OA\Items(type="integer"), nullable=true),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(
 *         property="images",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/Image"),
 *         description="Associated images"
 *     )
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
            'price' => $this->price,
            'discount' => $this->discount,
            'description' => $this->description,
            'gender' => $this->gender,
            'sizes' => json_decode($this->sizes),
            'separated_sizes' => json_decode($this->separated_sizes),
            'color' => $this->color,
            'manufacturer' => $this->manufacturer,
            'width' => $this->width,
            'height' => $this->height,
            'weight' => $this->weight,
            'production_time' => $this->production_time,
            'min_order' => $this->min_order,
            'seller_status' => $this->seller_status,
            'status' => $this->status,
            'shop_id' => $this->shop_id,
            'category_id' => $this->category_id,
            'brand_ids' => json_decode($this->brand_ids),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}