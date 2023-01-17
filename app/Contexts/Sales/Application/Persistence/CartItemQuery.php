<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Application\Persistence;

interface CartItemQuery
{
    public function paginate(int $currentPage): self;

    public function get(int $customerUserId): CartItemPaginator;
}
