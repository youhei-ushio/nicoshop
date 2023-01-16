<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Domain\Persistence;

use App\Contexts\Sales\Domain\Entity\Cart;

interface CartRepository
{
    public function save(Cart $cart): void;

    public function findByCustomerId(int $customerUserId): Cart;
}
