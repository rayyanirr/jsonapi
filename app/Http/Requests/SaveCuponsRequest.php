<?php

namespace App\Http\Requests;
use Illuminate\Validation\Rule;

use Illuminate\Foundation\Http\FormRequest;

class SaveCuponsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'data.id' => [
                Rule::requiredIf($this->route('cupon')),
                'exists:CORTESIAS,CODIGO',
            ],
            'data.attributes.name' => 'required|string',
            'data.attributes.email' => 'required|email',
            'data.attributes.cod-prod' => 'required',
            'data.attributes.type' => 'required|string',
            'data.attributes.type-courtesy' => 'required|string',
            'data.attributes.activation-date' => 'required',
            'data.attributes.expiration-date' => 'required',
        ];
    }
}
