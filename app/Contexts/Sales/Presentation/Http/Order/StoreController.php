<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Presentation\Http\Order;

use App\Contexts\Sales\Application\UseCase\Order\Store\Input;
use App\Contexts\Sales\Application\UseCase\Order\Store\Interactor;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Spatie\RouteAttributes\Attributes\Post;
use Throwable;

final class StoreController extends Controller
{
    #[Post('/sales/orders', 'sales.orders.store')]
    public function __invoke(
        StoreRequest $request,
        Interactor $interactor,
    ): Redirector|Application|RedirectResponse
    {
        $input = $request->validated();
        try {
            $interactor->execute(Input::fromArray($input));
            return redirect(route('sales.orders.index'))
                ->with('succeeded', 'Order created.');
        } catch (Throwable $exception) {
            return redirect(route('sales.orders.create'))
                ->with('failed', $exception->getMessage());
        }
    }
}
