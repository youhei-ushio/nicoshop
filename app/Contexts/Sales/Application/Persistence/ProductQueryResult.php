<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Application\Persistence;

final class ProductQueryResult
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
    )
    {

    }
}
