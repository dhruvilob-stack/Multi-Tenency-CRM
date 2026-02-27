<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use BelongsToOrganization;

    use HasFactory;

    protected $fillable = [
        'organization_id',
        'master_product_id',
        'brand_id',
        'category_id',
        'name',
        'sku',
        'price_cents',
        'sustainability_score',
        'carbon_kg_per_unit',
        'is_active',
        'description',
        'variant_options',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'bool',
            'sustainability_score' => 'decimal:2',
            'carbon_kg_per_unit' => 'decimal:4',
            'variant_options' => 'array',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function masterProduct(): BelongsTo
    {
        return $this->belongsTo(self::class, 'master_product_id');
    }

    public function clones(): HasMany
    {
        return $this->hasMany(self::class, 'master_product_id');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function suppliers(): BelongsToMany
    {
        return $this->belongsToMany(Supplier::class)->withTimestamps();
    }

    public function bomItems(): HasMany
    {
        return $this->hasMany(BomItem::class);
    }

    public function inventoryItems(): HasMany
    {
        return $this->hasMany(InventoryItem::class);
    }
}
