<?php

use App\Http\Controllers\Api\IntegrationMetaController;
use App\Http\Controllers\Api\MonitoringController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->middleware(['throttle:60,1', 'integration.key'])
    ->group(function (): void {
        Route::get('/integrations/meta', [IntegrationMetaController::class, 'index']);
        Route::get('/monitoring/system-overview', [MonitoringController::class, 'systemOverview']);
        Route::get('/monitoring/organizations/{organization}/performance', [MonitoringController::class, 'organizationPerformance']);
        Route::get('/monitoring/purchase-orders/{purchaseOrder}/flow', [MonitoringController::class, 'purchaseOrderFlow']);
    });
