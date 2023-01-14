<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Application\UseCase\Order\Store;

use App\Contexts\Sales\Domain\Value\Product;

final class Input
{
    /**
     * @param Product[] $products
     */
    private function __construct(
        public readonly array $products,
        public readonly int $customerUserId,
    )
    {

    }

    public static function fromArray(array $input): self
    {
        foreach ($input['items'] as $index => $item) {
            if (empty($item['product_id']) || empty($item['quantity'])) {
                unset($input['items'][$index]);
            }
        }
        return new self(
            products: array_map(function (array $itemInput) {
                return new Product(
                    intval($itemInput['product_id']),
                    intval($itemInput['quantity']),
                );
            }, $input['items']),
            customerUserId: intval($input['user_id']),
        );
    }
}
