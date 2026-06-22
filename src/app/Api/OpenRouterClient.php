<?php

declare(strict_types=1);

namespace App\Api;

use OpenAI;
use Exception;
use Illuminate\Support\Facades\Log;

final class OpenRouterClient
{
    const string BASE_URL = 'https://openrouter.ai';

    protected ?string $apiKey;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = config('services.openrouter.key');
        // Позволяет гибко менять модель через .env, по умолчанию используем случайную бесплатную от OpenRouter
        $this->model = config('services.openrouter.model');
    }


    /**
     * Отправляет промпт в OpenRouter и ожидает СТРОГО JSON на выходе.
     */
    public function sendJsonPrompt(string $prompt): ?array
    {
        if (empty($this->apiKey)) {
            Log::warning('OpenRouter API Key не задан. Запрос пропущен.');
            return null;
        }

        try {
            $client = OpenAI::factory()
                ->withApiKey($this->apiKey)
                ->withBaseUri(self::BASE_URL)
                ->make();

            $response = $client->chat()->create([
                'model' => $this->model,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.5,
                'response_format' => ['type' => 'json_object']
            ]);

            Log::info($response->toArray());
            $content = $response->choices[0]->message->content;


            // Извлечение JSON из markdown-окружения
            if (preg_match('/\{.*\}/s', $content, $matches)) {
                $cleanContent = $matches[0];
            } else {
                $cleanContent = $content;
            }

            $decoded = json_decode($cleanContent, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Не удалось распарсить JSON от OpenRouter. Ошибка: ' . json_last_error_msg() . ' | Ответ: ' . $content);
                return null;
            }

            return $decoded;

        } catch (Exception $e) {
            Log::error('Ошибка на уровне сетевого запроса к OpenRouter: ' . $e->getMessage());
            return null;
        }
    }
}
