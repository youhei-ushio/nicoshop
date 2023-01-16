<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $customer_user_id
 * @property Collection $items
 */
final class Cart extends Model
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

    ];

    protected $casts = [

    ];

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }
}
