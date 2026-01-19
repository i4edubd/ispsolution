<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTenantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole('superadmin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'domain' => 'nullable|string|max:255|unique:tenants,domain',
            'subdomain' => 'nullable|string|max:255|unique:tenants,subdomain|regex:/^[a-z0-9\-]+$/',
            'database' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive,suspended',
            'max_users' => 'nullable|integer|min:1',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The tenant name is required.',
            'domain.unique' => 'This domain is already in use.',
            'subdomain.unique' => 'This subdomain is already taken.',
            'subdomain.regex' => 'Subdomain can only contain lowercase letters, numbers, and hyphens.',
            'status.required' => 'Please select a status.',
            'status.in' => 'Invalid status selected.',
            'max_users.min' => 'Maximum users must be at least 1.',
            'contact_email.email' => 'Please enter a valid contact email.',
        ];
    }
}
