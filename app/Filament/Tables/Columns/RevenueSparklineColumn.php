<?php

namespace App\Filament\Tables\Columns;

use Closure;
use Filament\Tables\Columns\Column;

class RevenueSparklineColumn extends Column
{
    protected string $view = 'filament.tables.columns.revenue-sparkline-column';

    protected array|Closure|null $data = null;

    public function data(array|Closure|null $data): static
    {
        $this->data = $data;

        return $this;
    }

    public function getData(): array
    {
        return $this->evaluate($this->data) ?? [];
    }
}
