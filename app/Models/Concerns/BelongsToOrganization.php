<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait BelongsToOrganization
{
    public static function bootBelongsToOrganization(): void
    {
        static::addGlobalScope('organization', function (Builder $builder): void {
            $user = auth()->user();

            if (! $user || $user->isSuperAdmin()) {
                return;
            }

            if ($user->organization_id === null) {
                return;
            }

            $builder->where($builder->getModel()->getTable().'.organization_id', $user->organization_id);
        });

        static::creating(function (Model $model): void {
            $user = auth()->user();

            if (! $user || $user->isSuperAdmin()) {
                return;
            }

            if ($model->organization_id === null) {
                $model->organization_id = $user->organization_id;
            }
        });
    }
}
