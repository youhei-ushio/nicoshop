<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Application\UseCase\Order\Store;

use App\Contexts\Sales\Domain\Entity\OrderFactory;
use App\Contexts\Sales\Domain\Persistence\OrderRepository;

/**
 * 注文の新規登録
 */
final class Interactor
{
    public function __construct(
        private readonly OrderFactory $orderFactory,
        private readonly OrderRepository $orderRepository,
    )
    {

    }

    public function execute(Input $input): void
    {
        $order = $this->orderFactory->create(
            $input->customerUserId,
            $input->products,
        );
        $this->orderRepository->save($order);
    }
}
