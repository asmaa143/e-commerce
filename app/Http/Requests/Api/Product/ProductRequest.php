<?php

namespace App\Http\Requests\Api\Product;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
            "name"=>"nullable|string",
            "category_id"=>"nullable",
            "min_price"=>"nullable|numeric",
            "max_price"=>"nullable|numeric",
            "per_page"=>"nullable|numeric|integer",
        ];
    }
}
