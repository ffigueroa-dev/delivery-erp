<?php

namespace App\Modules\Product\Http\Requests;

use App\Modules\Product\Enums\PriceType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:products,name,' . $this->route('product')->id,
            'description' => 'nullable|string|max:255',
            'active' => 'required|boolean',
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
