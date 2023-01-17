<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Presentation\Http\Controller\Cart;

use App\Contexts\Sales\Application\UseCase\Cart\Detail\Input;
use App\Contexts\Sales\Application\UseCase\Cart\Detail\Interactor;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Spatie\RouteAttributes\Attributes\Get;

final class DetailController extends Controller
{
    #[Get('/sales/cart', 'sales.cart.detail')]
    public function __invoke(
        DetailRequest $request,
        Interactor $interactor,
    ): Factory|View|Application
    {
        $cart = $interactor->execute(new Input(
            auth()->id(),
            intval($request->validated('page', 1))
        ));
        return view('sales::page/cart/detail')
            ->with('cart', $cart);
    }
}
