<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Quotation extends Model
{
    /** @use HasFactory<\Database\Factories\QuotationFactory> */
    use HasFactory;

    protected $fillable = [
        'rfq_id',
        'total_amount_cents',
        'status',
    ];

    public function rfq(): BelongsTo
    {
        return $this->belongsTo(Rfq::class);
    }
}
