<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateQueueRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return \Illuminate\Support\Facades\Auth::check() && \Illuminate\Support\Facades\Auth::user()->hasAnyRole(['Admin', 'Staff']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'service_id' => 'required|exists:services,id',
            'patient_name' => 'required|string|max:255|regex:/^[a-zA-Z\s\.\-\']+$/u',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'service_id.required' => 'Please select a service.',
            'service_id.exists' => 'The selected service is invalid.',
            'patient_name.required' => 'Patient name is required.',
            'patient_name.regex' => 'Patient name contains invalid characters.',
            'patient_name.max' => 'Patient name cannot exceed 255 characters.',
        ];
    }
}
