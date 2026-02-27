<?php

namespace App\Filament\Organization\Resources\Users\Pages;

use App\Filament\Organization\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['organization_id'] = auth()->user()?->organization_id;

        if (isset($data['role_id'])) {
            $roleName = \App\Models\Role::query()->whereKey($data['role_id'])->value('name');
            $data['role'] = $roleName ?? $data['role'];
        }

        return $data;
    }
}
