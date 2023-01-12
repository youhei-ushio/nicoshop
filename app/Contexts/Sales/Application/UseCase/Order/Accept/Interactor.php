<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Application\UseCase\Order\Accept;

use App\Contexts\Sales\Domain\Persistence\OrderRepository;

/**
 * 注文受付
 */
final class Interactor
{
    public function __construct(
        private readonly OrderRepository $orderRepository,
    )
    {

    }

    public function execute(Input $input): void
    {
        $order = $this->orderRepository->findById($input->id);
        $order->accept();
        $this->orderRepository->save($order);
    }
}
