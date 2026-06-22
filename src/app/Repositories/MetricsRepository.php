<?php

declare(strict_types=1);

namespace App\Repositories;

use Exception;
use Illuminate\Support\Facades\Log;

final class MetricsRepository
{
    private string $metricsFile;

    public function __construct()
    {
        $this->metricsFile = storage_path('app/contact_metrics.json');
    }

    public function getRawMetrics(): ?array
    {
        if (!file_exists($this->metricsFile)) {
            return null;
        }

        $content = file_get_contents($this->metricsFile);
        return json_decode($content, true) ?: null;
    }

    public function incrementMetrics(string $category): void
    {
        $metrics = $this->getRawMetrics() ?? ['total_requests' => 0, 'by_category' => []];

        $metrics['total_requests']++;
        $metrics['by_category'][$category] = ($metrics['by_category'][$category] ?? 0) + 1;

        try {
            file_put_contents(
                $this->metricsFile,
                json_encode($metrics, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            );
        } catch (Exception $e) {
            Log::error('Ошибка при попытке обновить метрики: ' . $e->getMessage());
        }
    }
}
