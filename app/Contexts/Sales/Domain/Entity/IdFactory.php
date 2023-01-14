<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Domain\Entity;

interface IdFactory
{
    public function create(): string;
}
