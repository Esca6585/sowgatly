<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="AddressRequest",
 *     @OA\Property(property="street", type="string"),
 *     @OA\Property(property="city", type="string"),
 *     @OA\Property(property="state", type="string"),
 *     @OA\Property(property="country", type="string"),
 *     @OA\Property(property="postal_code", type="string")
 * )
 */
class AddressRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'street' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
        ];
    }
}