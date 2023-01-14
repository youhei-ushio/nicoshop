<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Infrastructure\Factory;

use App\Contexts\Sales\Domain\Entity\IdFactory;
use Illuminate\Support\Str;

final class IdFactoryImpl implements IdFactory
{
    public function create(): string
    {
        return Str::uuid()->toString();
    }
}
