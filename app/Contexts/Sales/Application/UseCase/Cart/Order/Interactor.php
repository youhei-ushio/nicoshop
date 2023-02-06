<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Application\UseCase\Cart\Order;

use App\Contexts\Sales\Domain\Entity\OrderFactory;
use App\Contexts\Sales\Domain\Persistence\CartRepository;
use App\Contexts\Sales\Domain\Persistence\OrderRepository;

/**
 * 注文
 */
final class Interactor
{
    public function __construct(
        private readonly CartRepository $cartRepository,
        private readonly OrderFactory $orderFactory,
        private readonly OrderRepository $orderRepository,
    )
    {

    }

    public function execute(Input $input): void
    {
        $cart = $this->cartRepository->findByCustomerId($input->customerUserId);

        $order = $this->orderFactory->create(
            $input->customerUserId,
            $cart->toOrder(),
        );
        $this->orderRepository->save($order);

        $cart->clear();
        $this->cartRepository->save($cart);
    }
}
