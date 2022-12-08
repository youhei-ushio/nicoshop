<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Application\UseCase\Order\Store;

use App\Contexts\Sales\Application\Persistence\ProductQuery;
use App\Contexts\Sales\Domain\Entity\Order;
use App\Contexts\Sales\Domain\Persistence\OrderRepository;

/**
 * 注文の新規登録
 */
final class Interactor
{
    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly ProductQuery $productQuery,
    )
    {

    }

    public function execute(Input $input): void
    {
        $products = $this->productQuery->filterByIds(
            array_map(function (Input\OrderItem $orderItem) {
                return $orderItem->productId;
            }, $input->items)
        )->get();

        $order = Order::create($input->customerUserId);
        foreach ($input->items as $orderItem) {
            $order->add($products->getById($orderItem->productId), $orderItem->quantity);
        }
        $order->save($this->orderRepository);
    }
}
