<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryItem extends Model
{
    /** @use HasFactory<\Database\Factories\InventoryItemFactory> */
    use BelongsToOrganization;

    use HasFactory;

    protected $fillable = [
        'organization_id',
        'product_id',
        'quantity_on_hand',
        'reorder_threshold',
        'expiry_tracking_enabled',
        'next_expiry_date',
    ];

    protected function casts(): array
    {
        return [
            'expiry_tracking_enabled' => 'bool',
            'next_expiry_date' => 'date',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function batches(): HasMany
    {
        return $this->hasMany(InventoryBatch::class);
    }
}
