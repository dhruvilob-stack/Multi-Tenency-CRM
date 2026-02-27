<?php

namespace App\Filament\AvatarProviders;

use Filament\AvatarProviders\Contracts\AvatarProvider;
use Filament\Facades\Filament;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class TwoLetterUiAvatarsProvider implements AvatarProvider
{
    public function get(Model|Authenticatable $record): string
    {
        $rawName = (string) Filament::getNameForDefaultAvatar($record);

        $letters = preg_replace('/\s+/u', '', trim($rawName)) ?? '';
        $first = mb_substr($letters, 0, 1);
        $second = mb_substr($letters, 1, 1);

        $initials = trim(mb_strtoupper(trim($first.' '.$second)));

        return 'https://ui-avatars.com/api/?name='
            .urlencode($initials)
            .'&color=FFFFFF&background='
            .urlencode(FilamentColor::getColor('gray')[950] ?? Color::Gray[950]);
    }
}
