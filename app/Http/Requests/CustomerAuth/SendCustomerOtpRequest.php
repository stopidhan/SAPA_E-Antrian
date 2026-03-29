<?php

namespace App\Http\Requests\CustomerAuth;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\TurnstileRule;

class SendCustomerOtpRequest extends FormRequest
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
            'nama' => ['required', 'string', 'max:100'],
            'whatsapp' => ['required', 'string', 'min:10', 'max:15', 'regex:/^[0-9]+$/'],
            'cf-turnstile-response' => ['required', 'string', new TurnstileRule()],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'cf-turnstile-response.required' => 'Verifikasi keamanan (Turnstile) wajib dicentang.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'nama' => trim((string) $this->input('nama')),
            'whatsapp' => preg_replace('/\D+/', '', (string) $this->input('whatsapp')),
        ]);
    }
}
