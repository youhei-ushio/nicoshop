<?php

declare(strict_types=1);

namespace Tests\Unit\Sales\Cart\Mock;

use App\Contexts\Sales\Domain\Entity\Cart;
use App\Contexts\Sales\Domain\Persistence\CartRecord;
use App\Contexts\Sales\Domain\Persistence\CartRepository;
use App\Contexts\Sales\Domain\Persistence\EventChannel;
use Closure;

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
        $this->items =  Closure::bind(function() use ($cart) {
            return $cart->toPersistenceRecord()->items;
        }, null, Cart::class)->__invoke();
    }

    public function findByCustomerId(int $customerUserId): Cart
    {
        $items = $this->items;
        $eventChannel = $this->eventChannel;
        return Closure::bind(function() use ($customerUserId, $items, $eventChannel) {
            return Cart::restore(
                new CartRecord(
                    $customerUserId,
                    $items,
                ),
                $eventChannel,
            );
        }, null, Cart::class)->__invoke();
    }

    public function getItems(): array
    {
        return $this->items;
    }
}
