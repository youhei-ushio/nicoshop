<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Presentation\Http\Order;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Spatie\RouteAttributes\Attributes\Get;

final class CreateController extends Controller
{
    #[Get('/sales/orders/create', 'sales.orders.create')]
    public function __invoke(): Factory|View|Application
    {
        return view('sales::order/create');
    }
}
