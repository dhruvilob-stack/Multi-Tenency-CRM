<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    /** @use HasFactory<\Database\Factories\PurchaseOrderFactory> */
    use HasFactory;

    protected $fillable = [
        'buyer_id',
        'supplier_id',
        'order_number',
        'status',
        'total_amount_cents',
        'approved_by',
        'blockchain_tx_hash',
    ];

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'buyer_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function grns(): HasMany
    {
        return $this->hasMany(Grn::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }

    public function dispatchOrders(): HasMany
    {
        return $this->hasMany(DispatchOrder::class);
    }
}
