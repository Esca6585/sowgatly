<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'discount' => 'nullable|integer|min:0',
            'description' => 'required|string',
            'gender' => 'nullable|string',
            'sizes' => 'nullable|json',
            'separated_sizes' => 'nullable|json',
            'color' => 'nullable|string',
            'manufacturer' => 'nullable|string',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'weight' => 'nullable|numeric|min:0',
            'production_time' => 'nullable|integer|min:0',
            'min_order' => 'nullable|integer|min:0',
            'seller_status' => 'required|boolean',
            'status' => 'required|boolean',
            'shop_id' => 'required|exists:shops,id',
            'category_id' => 'required|exists:categories,id',
            'brand_ids' => 'nullable|json',
            'images' => 'nullable|array',
            'images.*' => [
                'required',
                'string',
                'regex:/^data:image\/(\w+);base64,/',
            ],
        ];
    }
}