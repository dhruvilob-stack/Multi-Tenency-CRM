<?php

namespace App\Filament\Organization\Resources\Products\Pages;

use App\Filament\Organization\Resources\Products\ProductResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;
}
