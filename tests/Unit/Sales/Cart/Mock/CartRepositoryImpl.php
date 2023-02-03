<?php

declare(strict_types=1);

namespace Tests\Unit\Sales\Cart\Mock;

use App\Contexts\Sales\Domain\Entity\Cart;
use App\Contexts\Sales\Domain\Persistence\CartRecord;
use App\Contexts\Sales\Domain\Persistence\CartRepository;
use App\Contexts\Sales\Domain\Persistence\EventChannel;

final class CartRepositoryImpl implements CartRepository
{
    private array $items = [];

    public function __construct(
        private readonly EventChannel $eventChannel,
    )
    {

    }

    public function save(Cart $cart): void
    {
        $this->items = $cart->toPersistenceRecord()->items;
    }

    public function findByCustomerId(int $customerUserId): Cart
    {
        return Cart::restore(
            new CartRecord(
                $customerUserId,
                $this->items,
            ),
            $this->eventChannel,
        );
    }

    public function getItems(): array
    {
        return $this->items;
    }
}
