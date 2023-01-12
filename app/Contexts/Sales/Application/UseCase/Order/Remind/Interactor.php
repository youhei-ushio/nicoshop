<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Application\UseCase\Order\Remind;

use App\Contexts\Sales\Domain\Persistence\OrderRepository;

/**
 * 未受付のリマインド
 */
final class Interactor
{
    public function __construct(
        private readonly OrderRepository $repository,
    )
    {

    }

    public function execute(): void
    {
        foreach ($this->repository->findUnacceptedOrder() as $order) {
            $order->remind();
        }
    }
}
