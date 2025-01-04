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
            'mini_logo'         => ['nullable', 'image', 'mimes:png,jpg,webp,jpeg'],
            'favicon'           => ['nullable', 'image', 'mimes:png,jpg'],
        ];
    }

    public function messages()
    {
        return [
            'logo.image'      => 'The System logo must be an image',
            'mini_logo.image' => 'The Mini logo must be an image'
        ];
    }
}
