<?php

declare(strict_types=1);

namespace Tests\Unit\Sales\Cart\Mock;

use App\Contexts\Sales\Domain\Entity\Cart;
use App\Contexts\Sales\Domain\Persistence\CartRecord;
use App\Contexts\Sales\Domain\Persistence\CartRepository;
use Seasalt\Nicoca\Components\Domain\EventChannel;
use Seasalt\Nicoca\Components\Infrastructure\Persistence\EntityRepositoryImpl;

final class CartRepositoryImpl extends EntityRepositoryImpl implements CartRepository
{
    private array $items = [];

    public function __construct(EventChannel $eventChannel)
    {
        parent::__construct($eventChannel, Cart::class);
    }

    public function save(Cart $cart): void
    {
        $record = $this->createRecordFromEntity($cart);
        $this->items =  $record->items;
    }

    public function findByCustomerId(int $customerUserId): Cart
    {
        $items = $this->items;
        return $this->restoreEntity(new CartRecord(
            $customerUserId,
            $items,
        ));
    }

    public function getItems(): array
    {
        return $this->items;
    }
}
