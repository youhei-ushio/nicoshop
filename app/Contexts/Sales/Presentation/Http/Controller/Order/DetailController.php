<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Presentation\Http\Controller\Order;

use App\Contexts\Sales\Application\UseCase\Order\Detail\Input;
use App\Contexts\Sales\Application\UseCase\Order\Detail\Interactor;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\WhereUlid;

final class DetailController extends Controller
{
    #[Get('/sales/orders/{id}', 'sales.orders.detail')]
    #[WhereUlid('id')]
    public function __invoke(
        string $id,
        Interactor $interactor,
    ): View|Factory|Redirector|Application|RedirectResponse {
        try {
            $order = $interactor->execute(new Input($id));
            return view('sales::page/order/detail')
                ->with('order', $order);
        } catch (Exception $exception) {
            return redirect(route('sales.orders.index'))
                ->with('failed', $exception->getMessage());
        }
    }
}
