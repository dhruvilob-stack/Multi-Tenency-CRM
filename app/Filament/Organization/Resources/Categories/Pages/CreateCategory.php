<?php

namespace App\Filament\Organization\Resources\Categories\Pages;

use App\Filament\Organization\Resources\Categories\CategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;
}
