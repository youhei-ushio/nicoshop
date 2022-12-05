<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Domain\Notification;

interface OrderCreated
{
    public function notify(): void;
}
