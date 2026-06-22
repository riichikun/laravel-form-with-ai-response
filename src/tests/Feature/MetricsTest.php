<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class MetricsTest extends TestCase
{
    public function test_metrics_controller(): void
    {
        $response = $this->get('/api/metrics');

        $response->assertStatus(200);
    }
}
