<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreBookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'isbn' => ['required', 'string', 'regex:/^[0-9]+$/', 'min :10', 'max:13', 'unique:books'],
            'title' => ['required', 'string', 'max:100'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:100'],
            'published' => ['nullable', 'date_format:Y-m-d'],
            'publisher' => ['required', 'string', 'max:255'],
            'pages' => ['nullable', 'integer', 'min:1'],
            'description' => ['nullable', 'string'],
            'website' => ['nullable', 'string', 'url']
        ];
    }
    protected function failedValidation (Validator $validator) {
        throw new HttpResponseException(response(['errors' => $validator->getMessageBag()], 422));
    }
}
