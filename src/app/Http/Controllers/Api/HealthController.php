<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

final class HealthController extends Controller
{
    #[OA\Get(
        path: "/api/health",
        description: "Возвращает текущий статус системы, время сервера, версию PHP и текущее окружение.",
        summary: "Проверка работоспособности приложения (Health Check)",
        tags: ["system"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Система работает нормально",
                content: new OA\JsonContent(
                    required: ["status", "timestamp", "php_version", "environment"],
                    properties: [
                        new OA\Property(property: "status", type: "string", example: "UP"),
                        new OA\Property(property: "timestamp", type: "string", format: "date-time", example: "2026-06-19T09:15:00+00:00"),
                        new OA\Property(property: "php_version", type: "string", example: "8.2.12"),
                        new OA\Property(property: "environment", type: "string", example: "production")
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: "Ошибка сервера"
            )
        ]
    )]
    public function health(): JsonResponse
    {
        return response()->json([
            'status' => 'UP',
            'timestamp' => now()->toIso8601String(),
            'php_version' => PHP_VERSION,
            'environment' => app()->environment()
        ], 200);
    }
}
