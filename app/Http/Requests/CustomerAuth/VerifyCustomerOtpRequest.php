<?php

namespace App\Http\Requests\CustomerAuth;

use Illuminate\Foundation\Http\FormRequest;

class VerifyCustomerOtpRequest extends FormRequest
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
            'whatsapp' => ['required', 'string', 'min:10', 'max:15', 'regex:/^[0-9]+$/'],
            'otp_code' => ['required', 'digits:6'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'whatsapp' => preg_replace('/\D+/', '', (string) $this->input('whatsapp')),
            'otp_code' => preg_replace('/\D+/', '', (string) $this->input('otp_code')),
        ]);
    }
}
