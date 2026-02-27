<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BomItem extends Model
{
    /** @use HasFactory<\Database\Factories\BomItemFactory> */
    use HasFactory;

    protected $fillable = [
        'product_id',
        'raw_material_id',
        'alternative_raw_material_id',
        'quantity_required',
        'unit_cost_cents',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function rawMaterial(): BelongsTo
    {
        return $this->belongsTo(RawMaterial::class);
    }

    public function alternativeRawMaterial(): BelongsTo
    {
        return $this->belongsTo(RawMaterial::class, 'alternative_raw_material_id');
    }
}
