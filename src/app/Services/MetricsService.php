<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\MetricsRepository;

final readonly class MetricsService
{
    public function __construct(private MetricsRepository $MetricsRepository) {}

    public function getMetrics(): array
    {
        $metrics = $this->MetricsRepository->getRawMetrics();

        if ($metrics === null) {
            return [
                'total_requests' => 0,
                'by_category' => []
            ];
        }

        return $metrics;
    }
}
