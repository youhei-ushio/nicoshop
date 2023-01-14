<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $uuid
 * @property int $customer_user_id
 * @property Carbon $order_date
 * @property bool $accepted
 * @property Carbon|null $accepted_at
 * @property bool $finished
 * @property Carbon|null $finished_at
 * @property Collection $items
 */
final class Order extends Model
{
    protected $connection = 'mysql';

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    protected $hidden = [
        'id',
        'created_at',
        'updated_at',
    ];

    protected $dates = [
        'order_date',
        'accepted_at',
        'finished_at',
    ];

    protected $casts = [
        'accepted' => 'boolean',
        'finished' => 'boolean',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
