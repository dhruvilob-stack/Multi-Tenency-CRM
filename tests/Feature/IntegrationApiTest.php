<?php

namespace Tests\Feature;

use Tests\TestCase;

class IntegrationApiTest extends TestCase
{
    public function test_integration_meta_endpoint_rejects_invalid_key(): void
    {
        config()->set('integrations.api_key', 'test-secret-key');

        $response = $this->getJson('/api/v1/integrations/meta');

        $response->assertUnauthorized();
    }

    public function test_integration_meta_endpoint_returns_data_with_valid_key(): void
    {
        config()->set('integrations.api_key', 'test-secret-key');

        $response = $this->withHeaders([
            'X-Integration-Key' => 'test-secret-key',
        ])->getJson('/api/v1/integrations/meta');

        $response
            ->assertOk()
            ->assertJsonStructure([
                'system' => ['name', 'environment', 'live_updates'],
                'free_apis',
            ]);
    }
}
