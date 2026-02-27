<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Organization extends Model
{
    /** @use HasFactory<\Database\Factories\OrganizationFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'tenant_code',
        'type',
        'gst_number',
        'address',
        'contact_person',
        'status',
        'currency_code',
        'esg_score',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'esg_score' => 'decimal:2',
        ];
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function brands(): HasMany
    {
        return $this->hasMany(Brand::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function suppliers(): HasMany
    {
        return $this->hasMany(Supplier::class);
    }

    public function assignedSuppliers(): BelongsToMany
    {
        return $this->belongsToMany(
            Organization::class,
            'organization_supplier',
            'buyer_id',
            'supplier_id'
        )->withTimestamps();
    }

    public function assignedBuyers(): BelongsToMany
    {
        return $this->belongsToMany(
            Organization::class,
            'organization_supplier',
            'supplier_id',
            'buyer_id'
        )->withTimestamps();
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function revenueSnapshots(): HasMany
    {
        return $this->hasMany(OrganizationRevenueSnapshot::class);
    }

    public function rawMaterials(): HasMany
    {
        return $this->hasMany(RawMaterial::class);
    }

    public function inventoryItems(): HasMany
    {
        return $this->hasMany(InventoryItem::class);
    }

    public function productionOrders(): HasMany
    {
        return $this->hasMany(ProductionOrder::class);
    }

    public function purchaseOrdersAsBuyer(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class, 'buyer_id');
    }

    public function purchaseOrdersAsSupplier(): HasManyThrough
    {
        return $this->hasManyThrough(
            PurchaseOrder::class,
            Supplier::class,
            'organization_id',
            'supplier_id',
            'id',
            'id'
        );
    }

    public function rfqsAsBuyer(): HasMany
    {
        return $this->hasMany(Rfq::class, 'buyer_id');
    }

    public function rfqsAsSupplier(): HasMany
    {
        return $this->hasMany(Rfq::class, 'supplier_id');
    }

    public function latestRevenueSnapshot(): HasOne
    {
        return $this->hasOne(OrganizationRevenueSnapshot::class)->latestOfMany('recorded_at');
    }

    public function recentRevenueSnapshots(): HasMany
    {
        return $this->revenueSnapshots()->latest('recorded_at')->limit(12);
    }
}
