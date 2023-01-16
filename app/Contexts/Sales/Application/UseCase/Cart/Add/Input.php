<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Application\UseCase\Cart\Add;

use App\Contexts\Sales\Domain\Value\Product;

final class Input
{
    private function __construct(
        public readonly int $customerUserId,
        public readonly Product $product,
    )
    {

    }

    public static function fromInput(array $input): self
    {
        return new self(
            $input['customer_user_id'],
            new Product(
                $input['product_id'],
                $input['quantity'],
            ),
        );
    }
}
