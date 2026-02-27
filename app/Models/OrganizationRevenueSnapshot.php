<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrganizationRevenueSnapshot extends Model
{
    /** @use HasFactory<\Database\Factories\OrganizationRevenueSnapshotFactory> */
    use BelongsToOrganization;

    use HasFactory;

    protected $fillable = [
        'organization_id',
        'revenue_cents',
        'recorded_at',
    ];

    protected function casts(): array
    {
        return [
            'recorded_at' => 'datetime',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
