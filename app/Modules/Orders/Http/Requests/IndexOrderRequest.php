<?php

namespace App\Modules\Orders\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Modules\Orders\Enums\OrderStatus;
use Illuminate\Validation\Rule;

class IndexOrderRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'per_page' => $this->input('per_page', 50),
        ]);
    }
    public function rules(): array
    {
        return [
            'per_page' => [
                'sometimes',
                'integer',
                'min:1',
                'max:100',
            ],
            'page' => [
                'sometimes',
                'integer',
                'min:1',
            ],

            'date_from' => [
                'sometimes',
                'date_format:Y-m-d',
                'required_with:date_to',
                'before_or_equal:today',
            ],

            'date_to' => [
                'sometimes',
                'date_format:Y-m-d',
                'required_with:date_from',
                'after_or_equal:date_from',
                'before_or_equal:today',
            ],

            'sort' => ['sometimes', 'string', 'regex:/^-?created_at$/'],

            'status' => [
                'sometimes',
                'string',
                Rule::in(OrderStatus::values()),
            ],
            'client_id' => ['sometimes', 'string', 'uuid', 'exists:clients,id'],
            'delivery_id' => ['sometimes', 'string', 'uuid', 'exists:users,id'],
            'product_ids' => ['sometimes', 'array'],
            'product_ids.*' => ['string', 'uuid', 'exists:products,id'],

        ];
    }
}
