<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shipment extends Model
{
    /** @use HasFactory<\Database\Factories\ShipmentFactory> */
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'tracking_number',
        'status',
        'shipped_date',
        'delivered_date',
    ];

    protected function casts(): array
    {
        return [
            'shipped_date' => 'date',
            'delivered_date' => 'date',
        ];
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }
}
