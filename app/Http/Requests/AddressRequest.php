<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // You can add authorization logic here if needed
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
            'shop_id' => 'required|integer|exists:shops,id',
            'address_name' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'shop_id.required' => 'The shop ID is required.',
            'shop_id.integer' => 'The shop ID must be an integer.',
            'shop_id.exists' => 'The selected shop does not exist.',
            'address_name.required' => 'The address name is required.',
            'address_name.max' => 'The address name may not be greater than 255 characters.',
            'postal_code.required' => 'The postal code is required.',
            'postal_code.max' => 'The postal code may not be greater than 20 characters.',
        ];
    }
}