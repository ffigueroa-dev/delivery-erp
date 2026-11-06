<?php

namespace App\Modules\Clients\Http\Requests;

use App\Modules\Clients\Enums\ClientType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexClientRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'per_page' => $this->input('per_page', 50),
        ]);
        if ($this->input('active') === 'true') {
            $this->merge(['active' => true]);
        }

        if ($this->input('active') === 'false') {
            $this->merge(['active' => false]);
        }
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
            'active' => [
                'sometimes',
                'boolean',
            ],
            'type' => [
                'sometimes',
                'string',
                Rule::in(ClientType::values())
            ],
            'search' => [
                'sometimes',
                'string',
            ],
            'phone' => [
                'sometimes',
                'string',
            ],
            'address' => [
                'sometimes',
                'string',
            ],
        ];
    }
}
