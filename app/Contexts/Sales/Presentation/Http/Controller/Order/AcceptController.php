<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Presentation\Http\Controller\Order;

use App\Contexts\Sales\Application\UseCase\Order\Accept\Input;
use App\Contexts\Sales\Application\UseCase\Order\Accept\Interactor;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Spatie\RouteAttributes\Attributes\Post;
use Spatie\RouteAttributes\Attributes\WhereUuid;

final class AcceptController extends Controller
{
    #[Post('/sales/orders/{id}/accept', 'sales.orders.accept')]
    #[WhereUuid('id')]
    public function __invoke(
        string $id,
        Interactor $interactor,
    ): Redirector|Application|RedirectResponse {
        try {
            $interactor->execute(new Input($id));
            return redirect(route('sales.orders.index'))
                ->with('succeeded', 'Order accepted.');
        } catch (Exception $exception) {
            return redirect(route('sales.orders.index'))
                ->with('failed', $exception->getMessage());
        }
    }
}
