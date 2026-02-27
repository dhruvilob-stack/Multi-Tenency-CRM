<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class IntegrationMetaController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'system' => [
                'name' => config('app.name'),
                'environment' => config('app.env'),
                'live_updates' => [
                    'filament_spa' => true,
                    'livewire_reactive_forms' => true,
                ],
            ],
            'free_apis' => config('integrations.free_apis'),
        ]);
    }
}
