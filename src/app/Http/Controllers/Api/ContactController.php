<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\ContactFormMail;
use App\Repositories\MetricsRepository;
use App\Services\FeedbackAiAnalysisDTO;
use App\Services\FeedbackAiAnalysisService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Exception;
use OpenApi\Attributes as OA;


#[OA\Info(
    version: "1.0.0",
    description: "API позволяет отправлять сообщение через форму обратной связи и получать ИИ-генерированный ответ на электронную почту, указанную в форме.",
    title: "Форма обратной связи разработчика",
)]
final class ContactController extends Controller
{
    public function __construct(
        private readonly FeedbackAiAnalysisService $aiService,
        private readonly MetricsRepository $MetricsRepository
    ) {}


    #[OA\Post(
        path: "/api/contact",
        summary: "Отправка формы обратной связи",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "phone", "email", "comment"],
                properties: [
                    new OA\Property(property: "name", description: "Имя отправителя", type: "string", example: "Иван Иванов", maxLength: 255),
                    new OA\Property(property: "phone", description: "Контактный телефон", type: "string", example: "+7 (999) 123-45-67", maxLength: 50),
                    new OA\Property(property: "email", description: "Email", type: "string", format: "email", example: "ivan@example.com", maxLength: 255),
                    new OA\Property(property: "comment", description: "Текст обращения", type: "string", example: "Хочу обсудить сотрудничество.", maxLength: 5000, minLength: 5)
                ]
            )
        ),
        tags: ['user'],
        responses: [
            new OA\Response(
                response: 200,
                description: "Форма была успешно отправлена"
            ),
            new OA\Response(
                response: 422,
                description: "Ошибка валидации"
            ),
            new OA\Response(
                response: 500,
                description: "Ошибка сервера"
            )
        ],
    )]
    public function submit(Request $request): JsonResponse
    {
        Log::channel('single')->info('Получен запрос на /api/contact', [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'payload' => $request->except(['phone', 'email']) // Скрываем личные данные для GDPR
        ]);


        // Валидация входных данных
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'email' => 'required|email|max:255',
            'comment' => 'required|string|min:5|max:5000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();


        try {
            // AI-интеграция (анализ и генерация ответа с Graceful Fallback)
            $aiResult = $this->aiService->analyze(new FeedbackAiAnalysisDTO($validated['comment'], $validated['name']));
            $validated['ai_analysis'] = $aiResult;


            // Отправка писем
            $adminEmail = config('mail.from.address');


            // Письмо владельцу сайта
            Mail::to($adminEmail)->send(new ContactFormMail($validated, isAdminCopy: true));


            // Письмо пользователю с генерированным ответом
            Mail::to($validated['email'])->send(new ContactFormMail($validated, isAdminCopy: false));


            // Обновление файла статистики/метрики
            $this->MetricsRepository->incrementMetrics($aiResult['sentiment'] ?? 'unknown');

            return response()->json([
                'success' => true,
                'message' => 'Обращение успешно отправлено',
                'ai_processed' => !is_null($aiResult)
            ], 200);

        } catch (Exception $e) {
            Log::error('Критическая ошибка при обработке формы обратной связи: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Внутренняя ошибка сервера. Пожалуйста, попробуйте позже.'
            ], 500);
        }
    }
}
