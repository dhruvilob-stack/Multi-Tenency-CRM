<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * @var array<int, string>
     */
    private array $supportedLocales = [
        'en',
        'es',
        'fr',
        'hi',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $requestedLocale = $request->query('ln');

        if (is_string($requestedLocale) && in_array($requestedLocale, $this->supportedLocales, true)) {
            $request->session()->put('locale', $requestedLocale);

            if (($user = $request->user()) && Schema::hasColumn('users', 'locale')) {
                $user->forceFill([
                    'locale' => $requestedLocale,
                ])->save();
            }
        }

        $locale = $request->session()->get('locale');

        if (! is_string($locale) || blank($locale)) {
            $locale = Schema::hasColumn('users', 'locale') ? $request->user()?->locale : null;
        }

        if (is_string($locale) && filled($locale) && in_array($locale, $this->supportedLocales, true)) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}
