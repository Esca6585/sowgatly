<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="AddressRequest",
 *     @OA\Property(property="shop_id", type="integer", example=1),
 *     @OA\Property(property="address_name", type="string", example="123 Main St, Anytown"),
 *     @OA\Property(property="postal_code", type="string", example="744000")
 * )
 */
class AddressRequest extends FormRequest
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
            'shop_id' => 'required|integer|exists:shops,id',
            'address_name' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
        ];
    }
}