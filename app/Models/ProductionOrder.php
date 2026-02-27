<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductionOrder extends Model
{
    /** @use HasFactory<\Database\Factories\ProductionOrderFactory> */
    use BelongsToOrganization;

    use HasFactory;

    protected $fillable = [
        'organization_id',
        'product_id',
        'order_number',
        'quantity_planned',
        'quantity_completed',
        'status',
        'machine_utilization_percent',
        'defect_ratio_percent',
        'cost_per_unit_cents',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'machine_utilization_percent' => 'decimal:2',
            'defect_ratio_percent' => 'decimal:2',
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

    public function qualityReports(): HasMany
    {
        return $this->hasMany(QualityReport::class);
    }
}
