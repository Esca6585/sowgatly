<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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