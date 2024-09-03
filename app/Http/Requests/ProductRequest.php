<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="ProductRequest",
 *     title="Product Request",
 *     description="Product request body data",
 *     required={"name", "price", "description", "seller_status", "status", "shop_id", "category_id"},
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="price", type="number", format="float"),
 *     @OA\Property(property="discount", type="integer", nullable=true),
 *     @OA\Property(property="description", type="string"),
 *     @OA\Property(property="gender", type="string", nullable=true, description="Men, Women, Children and etc"),
 *     @OA\Property(property="sizes", type="array", @OA\Items(type="string"), nullable=true, description="42, 43,...,50 yaly olcegler"),
 *     @OA\Property(property="separated_sizes", type="array", @OA\Items(type="string"), nullable=true, description="S, M, L yaly olcegler"),
 *     @OA\Property(property="color", type="string", nullable=true),
 *     @OA\Property(property="manufacturer", type="string", nullable=true, description="Cykarylan yurdy"),
 *     @OA\Property(property="width", type="number", format="float", nullable=true),
 *     @OA\Property(property="height", type="number", format="float", nullable=true),
 *     @OA\Property(property="weight", type="number", format="float", nullable=true, description="Hemmesi gram gorunusinde bellenmeli"),
 *     @OA\Property(property="production_time", type="integer", nullable=true, description="Hemme product time minutda gorkeziler"),
 *     @OA\Property(property="min_order", type="integer", nullable=true),
 *     @OA\Property(property="seller_status", type="boolean", description="Bu dukancy tarapyndan berilmeli status"),
 *     @OA\Property(property="status", type="boolean", description="Bu administrator tarapyndan berilmeli status"),
 *     @OA\Property(property="shop_id", type="integer"),
 *     @OA\Property(property="category_id", type="integer"),
 *     @OA\Property(property="brand_ids", type="array", @OA\Items(type="integer"), nullable=true, description="Brand id-ler")
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
        ];
    }
}