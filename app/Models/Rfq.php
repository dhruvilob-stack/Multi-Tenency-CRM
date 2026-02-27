<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rfq extends Model
{
    /** @use HasFactory<\Database\Factories\RfqFactory> */
    use HasFactory;

    protected $fillable = [
        'buyer_id',
        'supplier_id',
        'rfq_number',
        'status',
        'due_date',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
        ];
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'buyer_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class);
    }
}
