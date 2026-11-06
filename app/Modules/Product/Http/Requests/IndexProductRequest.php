<?php

namespace App\Modules\Product\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexProductRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'per_page' => $this->input('per_page', 10),
        ]);
    }
    public function rules(): array
    {
        return [
            'search' => [
                'sometimes',
                'string',
            ],
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
        ];
    }
}
