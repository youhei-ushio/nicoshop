<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Presentation\Http\Order;

use Illuminate\Foundation\Http\FormRequest;

final class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => [
                'required',
                'integer',
                'min:1',
            ],
            'items' => [
                'required',
                'array',
            ],
            'items.*.product_id' => [
                'nullable',
                'integer',
                'min:1',
            ],
            'items.*.quantity' => [
                'nullable',
                'integer',
                'min:1',
            ],
        ];
    }
}
