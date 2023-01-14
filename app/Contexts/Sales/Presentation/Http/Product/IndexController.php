<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Presentation\Http\Product;

use App\Contexts\Sales\Application\UseCase\Product\Index\Input;
use App\Contexts\Sales\Application\UseCase\Product\Index\Interactor;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Spatie\RouteAttributes\Attributes\Get;

final class IndexController extends Controller
{
    #[Get('/sales/products', 'sales.products.index')]
    public function __invoke(
        IndexRequest $request,
        Interactor $interactor,
    ): Factory|View|Application
    {
        $input = $request->validated();
        $products = $interactor->execute(new Input(
            intval($input['limit'] ?? 100),
            intval($input['page'] ?? 1),
        ));
        return view('sales::product/index')
            ->with('products', $products);
    }
}
