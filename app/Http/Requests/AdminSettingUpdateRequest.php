<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminSettingUpdateRequest extends FormRequest
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
            'title'             => ['required', 'min:2'],
            'system_short_name' => ['required', 'min:2'],
            'logo'              => ['required', 'image', 'mimes:png,jpg,webp,jpeg'],
            'admin_mini_logo'         => ['nullable', 'image', 'mimes:png,jpg,webp,jpeg'],
            'admin_favicon'           => ['nullable', 'image', 'mimes:png,jpg'],
            'copyright'         => ['required', 'min:2'],
        ];
    }

    public function messages()
    {
        return [
            'logo'      => 'The System logo must be an image',
            'admin_mini_logo' => 'The Mini logo must be an image'
        ];
    }
}
