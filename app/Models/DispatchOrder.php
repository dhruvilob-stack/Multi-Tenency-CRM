<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DispatchOrder extends Model
{
    /** @use HasFactory<\Database\Factories\DispatchOrderFactory> */
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'dispatch_number',
        'status',
        'dispatched_at',
    ];

    protected function casts(): array
    {
        return [
            'dispatched_at' => 'datetime',
        ];
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }
}
