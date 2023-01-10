<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Application\UseCase\Order\Remind;

use App\Contexts\Sales\Domain\Persistence\NotAcceptedOrderRepository;

/**
 * 未受付のリマインド
 */
final class Interactor
{
    public function __construct(
        private readonly NotAcceptedOrderRepository $repository,
    )
    {

    }

    public function execute(): void
    {
        foreach ($this->repository->find() as $order) {
            $order->remind();
        }
    }
}
