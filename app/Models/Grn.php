<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Grn extends Model
{
    /** @use HasFactory<\Database\Factories\GrnFactory> */
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'received_by',
        'received_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'received_date' => 'date',
        ];
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
