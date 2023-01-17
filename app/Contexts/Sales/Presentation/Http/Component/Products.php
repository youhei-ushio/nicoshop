<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Presentation\Http\Component;

use App\Contexts\Sales\Application\UseCase\Cart\Add\Input;
use App\Contexts\Sales\Application\UseCase\Cart\Add\Interactor;
use App\Models;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use InvalidArgumentException;
use Livewire\Component;
use Livewire\WithPagination;
use Usernotnull\Toast\Concerns\WireToast;

final class Products extends Component
{
    use WithPagination;
    use WireToast;

    protected $queryString = [
        'page' => ['except' => 1],
    ];

    public function render(): Factory|View|Application
    {
        // Eloquent直呼び出しパターン
        return view('sales::component.products')
            ->with('products', Models\Product::query()->paginate(20));
    }

    public function add(Interactor $interactor, int $productId): void
    {
        try {
            $interactor->execute(
                Input::fromInput([
                    'customer_user_id' => auth()->id(),
                    'product_id' => $productId,
                    'quantity' => 1,
                ])
            );
            $this->emit('itemAdded');
            toast()->success('Cart item added!')->push();
        } catch (InvalidArgumentException $exception) {
            toast()->danger($exception->getMessage())->push();
        }
    }
}
