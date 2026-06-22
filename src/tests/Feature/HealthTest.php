<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class HealthTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_health_controller(): void
    {
        $response = $this->get('/api/health');

        $response->assertStatus(200);
    }
}
