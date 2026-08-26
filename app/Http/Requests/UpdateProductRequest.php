<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'price' => 'sometimes|required|numeric|min:0',
            'barcode' => 'nullable|string|max:255|unique:products,barcode,' . $this->route('id'),
            'category' => 'nullable|string|max:255',
            'stock_quantity' => 'sometimes|required|integer|min:0',
            'min_stock_alert' => 'sometimes|required|integer|min:0',
            'is_active' => 'boolean',
        ];
    }
}
