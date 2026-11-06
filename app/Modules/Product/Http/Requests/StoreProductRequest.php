<?php

namespace App\Modules\Product\Http\Requests;

use App\Modules\Product\Enums\PriceType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:products,name',
            ],

            'description' => [
                'nullable',
                'string',
                'max:255',
            ],

            'prices' => [
                'required',
                'array',
                'size:2',
            ],

            'prices.*.type' => [
                'required',
                Rule::enum(PriceType::class),
                'distinct',
            ],

            'prices.*.amount' => [
                'required',
                'integer',
                'min:0',
            ],
        ];
    }
}
