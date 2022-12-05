<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Application\UseCase\Order\Store;

final class Input
{
    /**
     * @param Input\OrderItem[] $items
     */
    private function __construct(
        public readonly array $items,
        public readonly int $customerUserId,
    )
    {

    }

    public static function fromArray(array $input): self
    {
        return new self(
            items: array_map(function (array $itemInput) {
                return new Input\OrderItem(
                    intval($itemInput['product_id']),
                    intval($itemInput['quantity']),
                );
            }, $input['items']),
            customerUserId: $input['user_id'],
        );
    }
}
