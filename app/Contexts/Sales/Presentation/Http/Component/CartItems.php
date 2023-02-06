<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Presentation\Http\Component;

use App\Contexts\Sales\Application\UseCase;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use InvalidArgumentException;
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
        $interactor = app()->make(UseCase\Cart\Detail\Interactor::class);
        $items = $interactor->execute(new UseCase\Cart\Detail\Input(
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

    public function clear(UseCase\Cart\Clear\Interactor $interactor): void
    {
        try {
            $interactor->execute(
                new UseCase\Cart\Clear\Input(
                    auth()->id(),
                )
            );
            $this->emit('itemCleared');
            toast()->success('Cart item cleared!')->push();
        } catch (InvalidArgumentException $exception) {
            toast()->danger($exception->getMessage())->push();
        }
    }

    public function order(UseCase\Cart\Order\Interactor $interactor): void
    {
        try {
            $interactor->execute(
                new UseCase\Cart\Order\Input(
                    auth()->id(),
                )
            );
            $this->emit('itemCleared');
            toast()->success('Order completed!')->push();
        } catch (InvalidArgumentException $exception) {
            toast()->danger($exception->getMessage())->push();
        }
    }
}
