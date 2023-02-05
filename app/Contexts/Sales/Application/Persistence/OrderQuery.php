<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Application\Persistence;

interface OrderQuery
{
    public function get(string $id): OrderQueryResult;
}
