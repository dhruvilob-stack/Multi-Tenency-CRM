<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QualityReport extends Model
{
    /** @use HasFactory<\Database\Factories\QualityReportFactory> */
    use HasFactory;

    protected $fillable = [
        'production_order_id',
        'status',
        'inspection_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'inspection_date' => 'date',
        ];
    }

    public function productionOrder(): BelongsTo
    {
        return $this->belongsTo(ProductionOrder::class);
    }
}
