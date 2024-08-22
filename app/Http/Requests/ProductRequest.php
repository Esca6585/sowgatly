<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="ProductRequest",
 *     required={"name", "price", "shop_id", "brand_id", "category_id"},
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="price", type="number", format="float"),
 *     @OA\Property(property="discount", type="integer", nullable=true),
 *     @OA\Property(property="description", type="string"),
 *     @OA\Property(property="gender", type="string", nullable=true),
 *     @OA\Property(property="sizes", type="string", nullable=true),
 *     @OA\Property(property="separated_sizes", type="string", nullable=true),
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
 *     @OA\Property(property="brand_id", type="integer"),
 *     @OA\Property(property="category_id", type="integer")
 * )
 */
class ProductRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string',
            'price' => 'required|numeric',
            'discount' => 'nullable|integer',
            'description' => 'required|string',
            'gender' => 'nullable|string',
            'sizes' => 'nullable|string',
            'separated_sizes' => 'nullable|string',
            'color' => 'nullable|string',
            'manufacturer' => 'nullable|string',
            'width' => 'nullable|numeric',
            'height' => 'nullable|numeric',
            'weight' => 'nullable|numeric',
            'production_time' => 'nullable|integer',
            'min_order' => 'nullable|integer',
            'seller_status' => 'boolean',
            'status' => 'boolean',
            'shop_id' => 'required|integer',
            'brand_id' => 'required|integer',
            'category_id' => 'required|integer',
        ];
    }
}