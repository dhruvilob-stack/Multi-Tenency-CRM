<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RawMaterial extends Model
{
    /** @use HasFactory<\Database\Factories\RawMaterialFactory> */
    use BelongsToOrganization;

    use HasFactory;

    protected $fillable = [
        'organization_id',
        'name',
        'sku',
        'unit',
        'unit_cost_cents',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'bool',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function bomItems(): HasMany
    {
        return $this->hasMany(BomItem::class);
    }
}
