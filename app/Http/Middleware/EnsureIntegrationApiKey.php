<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIntegrationApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $expectedApiKey = (string) config('integrations.api_key');
        $providedApiKey = (string) ($request->header('X-Integration-Key') ?? $request->query('api_key'));

        if ($expectedApiKey === '' || ! hash_equals($expectedApiKey, $providedApiKey)) {
            return response()->json([
                'message' => 'Invalid integration API key.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
