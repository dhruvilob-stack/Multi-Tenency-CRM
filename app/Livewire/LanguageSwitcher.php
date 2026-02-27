<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;

class LanguageSwitcher extends Component
{
    public string $locale = 'en';

    /**
     * @var array<string, array{label: string, flag: string}>
     */
    public array $languages = [
        'en' => ['label' => 'English', 'flag' => '🇺🇸'],
        'es' => ['label' => 'Español', 'flag' => '🇪🇸'],
        'fr' => ['label' => 'Français', 'flag' => '🇫🇷'],
        'hi' => ['label' => 'हिन्दी', 'flag' => '🇮🇳'],
    ];

    public function mount(): void
    {
        $this->locale = (string) (session('locale') ?? auth()->user()?->locale ?? App::getLocale() ?? 'en');

        if (! array_key_exists($this->locale, $this->languages)) {
            $this->locale = 'en';
        }
    }

    public function setLocale(string $locale): void
    {
        if (! array_key_exists($locale, $this->languages)) {
            return;
        }

        session()->put('locale', $locale);
        App::setLocale($locale);

        if (($user = auth()->user()) && Schema::hasColumn('users', 'locale')) {
            $user->forceFill([
                'locale' => $locale,
            ])->save();
        }

        $this->locale = $locale;

        $redirectTo = request()->header('referer') ?: url()->previous();

        if (! is_string($redirectTo) || blank($redirectTo) || str_contains($redirectTo, '/livewire')) {
            $redirectTo = url('/');
        }

        $separator = str_contains($redirectTo, '?') ? '&' : '?';

        if (str_contains($redirectTo, 'ln=')) {
            $redirectTo = preg_replace('/([?&])ln=[^&]*/', '$1ln='.$locale, $redirectTo) ?? $redirectTo;
        } else {
            $redirectTo .= $separator.'ln='.$locale;
        }

        $this->redirect($redirectTo, navigate: false);
    }

    public function render(): View
    {
        return view('livewire.language-switcher');
    }
}
