<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Presentation\Http\Controller\Cart;

use Illuminate\Foundation\Http\FormRequest;

final class DetailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'page' => [
                'nullable',
                'integer',
                'min:1',
            ],
        ];
    }
}
