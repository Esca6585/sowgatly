<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="ProductRequest",
 *     required={"name", "description", "price", "category_id"},
 *     @OA\Property(property="name", type="string", example="Cool T-Shirt"),
 *     @OA\Property(property="description", type="string", example="A comfortable and stylish t-shirt"),
 *     @OA\Property(property="price", type="number", format="float", example=29.99),
 *     @OA\Property(property="discount", type="number", format="float", example=5.00),
 *     @OA\Property(property="category_id", type="integer", example=1),
 *     @OA\Property(property="shop_id", type="integer", example=1),
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="gender", type="string", example="unisex"),
 *     @OA\Property(property="sizes", type="array", @OA\Items(type="string"), example={"S", "M", "L", "XL"}),
 *     @OA\Property(property="separated_sizes", type="array", @OA\Items(type="string"), example={"S", "M", "L", "XL"}),
 *     @OA\Property(property="color", type="string", example="Blue"),
 *     @OA\Property(property="manufacturer", type="string", example="FashionCo"),
 *     @OA\Property(property="width", type="number", format="float", example=30.5),
 *     @OA\Property(property="height", type="number", format="float", example=50.0),
 *     @OA\Property(property="weight", type="number", format="float", example=0.2),
 *     @OA\Property(property="production_time", type="integer", example=3),
 *     @OA\Property(property="min_order", type="integer", example=1),
 *     @OA\Property(property="seller_status", type="boolean", example=true)
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