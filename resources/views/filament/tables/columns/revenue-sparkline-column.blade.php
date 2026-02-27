@php
    $values = collect($getData())
        ->filter(fn ($value) => is_numeric($value))
        ->values();

    $count = $values->count();
    $width = 120;
    $height = 28;

    $min = $values->min() ?? 0;
    $max = $values->max() ?? 0;
    $range = max($max - $min, 1);

    $points = $values->map(function ($value, int $index) use ($count, $width, $height, $min, $range): array {
        $x = $count > 1 ? ($index * ($width / ($count - 1))) : 0;
        $y = $height - (($value - $min) / $range * $height);

        return [$x, $y];
    });

    $path = $points->map(function (array $point, int $index): string {
        $command = $index === 0 ? 'M' : 'L';

        return $command . number_format($point[0], 2, '.', '') . ' ' . number_format($point[1], 2, '.', '');
    })->implode(' ');
@endphp

<div {{ $getExtraAttributeBag()->class('flex items-center') }}>
    @if ($count === 0)
        <span class="text-xs text-gray-400">No data</span>
    @else
        <svg width="{{ $width }}" height="{{ $height }}" viewBox="0 0 {{ $width }} {{ $height }}" fill="none">
            <path d="{{ $path }}" stroke="currentColor" stroke-width="2" class="text-amber-500" />
        </svg>
    @endif
</div>
