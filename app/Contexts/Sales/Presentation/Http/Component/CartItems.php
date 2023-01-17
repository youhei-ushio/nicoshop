<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Presentation\Http\Component;

use App\Contexts\Sales\Application\UseCase\Cart\Detail\Input;
use App\Contexts\Sales\Application\UseCase\Cart\Detail\Interactor;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Component;
use Livewire\WithPagination;
use Usernotnull\Toast\Concerns\WireToast;

final class CartItems extends Component
{
    use WithPagination;
    use WireToast;

    protected $queryString = [
        'page' => ['except' => 1],
    ];

    public function render(): Factory|View|Application
    {
        // UseCase呼び出しパターン
        $interactor = app()->make(Interactor::class);
        $items = $interactor->execute(new Input(
            auth()->id(),
            $this->page,
        ));
        // links()のためLengthAwarePaginatorが必要
        $paginator = new LengthAwarePaginator(
            $items,
            $items->total(),
            $items->perPage(),
            $items->currentPage(),
        );
        return view('sales::component.cart-items')
            ->with('items', $paginator);
    }
}
