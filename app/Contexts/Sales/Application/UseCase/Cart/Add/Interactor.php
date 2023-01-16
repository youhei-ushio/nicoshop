<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Application\UseCase\Cart\Add;

use App\Contexts\Sales\Domain\Persistence\CartRepository;

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
        $cart->add($input->product);
        $this->cartRepository->save($cart);
    }
}
