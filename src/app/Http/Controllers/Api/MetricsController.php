<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MetricsService;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

final class MetricsController extends Controller
{
    protected string $metricsFile;

    public function __construct(private readonly MetricsService $MetricsService)
    {
        $this->metricsFile = storage_path('app/contact_metrics.json');
    }

    #[OA\Get(
        path: "/api/metrics",
        description: "Возвращает агрегированные метрики из файла статистики, включая общее количество запросов и разбивку по категориям AI-анализа.",
        summary: "Получение статистики обращений",
        tags: ["system"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Статистика успешно получена",
                content: new OA\JsonContent(
                    required: ["total_requests", "by_category"],
                    properties: [
                        new OA\Property(property: "total_requests", description: "Общее количество обработанных форм", type: "integer", example: 42),
                        new OA\Property(
                            property: "by_category",
                            description: "Разбивка метрик по категориям (например, тональность текста от AI)",
                            properties: [
                                new OA\Property(property: "positive", type: "integer", example: 25),
                                new OA\Property(property: "neutral", type: "integer", example: 12),
                                new OA\Property(property: "negative", type: "integer", example: 5),
                                new OA\Property(property: "unknown", type: "integer", example: 0)
                            ],
                            type: "object"
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: "Ошибка сервера"
            )
        ]

    )]
    public function metrics(): JsonResponse
    {
        $metrics = $this->MetricsService->getMetrics();

        return response()->json($metrics, 200);
    }
}
