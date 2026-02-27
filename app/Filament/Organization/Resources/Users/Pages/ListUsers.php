<?php

namespace App\Filament\Organization\Resources\Users\Pages;

use App\Filament\Organization\Resources\Users\UserResource;
use App\Filament\Organization\Resources\Users\Widgets\UsersOverview;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            UsersOverview::class,
        ];
    }
}
