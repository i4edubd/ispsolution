<?php

declare(strict_types=1);

namespace App\Http\Requests\HotspotLogin;

use Illuminate\Foundation\Http\FormRequest;

class VerifyLoginOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'mobile_number' => [
                'required',
                'string',
                'regex:/^[0-9]{10,15}$/',
            ],
            'otp_code' => [
                'required',
                'string',
                'size:6',
                'regex:/^[0-9]{6}$/',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'mobile_number.required' => 'Mobile number is required.',
            'mobile_number.regex' => 'Please enter a valid mobile number.',
            'otp_code.required' => 'OTP code is required.',
            'otp_code.size' => 'OTP must be 6 digits.',
            'otp_code.regex' => 'OTP must contain only numbers.',
        ];
    }
}
