<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Application\UseCase\Cart\Clear;

use App\Contexts\Sales\Domain\Persistence\CartRepository;

/**
 * カート内商品削除
 */
final class Interactor
{
    public function __construct(
        private readonly CartRepository $cartRepository,
    )
    {

    }

    public function execute(Input $input): void
    {
        $cart = $this->cartRepository->findByCustomerId($input->customerUserId);
        $cart->clear();
        $this->cartRepository->save($cart);
    }
}
