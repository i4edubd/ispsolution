<?php

declare(strict_types=1);

namespace App\Http\Requests\HotspotLogin;

use Illuminate\Foundation\Http\FormRequest;

class RequestLoginOtpRequest extends FormRequest
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
        ];
    }

    public function messages(): array
    {
        return [
            'mobile_number.required' => 'Mobile number is required.',
            'mobile_number.regex' => 'Please enter a valid mobile number (10-15 digits).',
        ];
    }
}
