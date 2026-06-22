<?php

declare(strict_types=1);

namespace App\Services;

use App\Api\OpenRouterClient;

final readonly class FeedbackAiAnalysisService
{
    public function __construct(private OpenRouterClient $aiClient) {}


    /**
     * Анализирует комментарий пользователя для формы обратной связи.
     */
    public function analyze(FeedbackAiAnalysisDTO $dto): array
    {
        $prompt = "Ты — AI-ассистент разработчика. Проанализируй следующее сообщение от потенциального клиента по имени".$dto->getName().".\n" .
            "Сообщение: ".$dto->getComment()."\n\n" .
            "Ответь СТРОГО в формате JSON (без разметки markdown markdown ```json) со следующими полями:\n" .
            "{\n" .
            "  \"sentiment\": \"positive\" или \"neutral\" или \"negative\",\n" .
            "  \"suggestedReply\": \"Текст вежливого ответа на русском языке с обращением по имени\"\n" .
            "}";


        $result = $this->aiClient->sendJsonPrompt($prompt);


        // Если API лежит, бизнес-логика не ломается, а возвращает безопасные заглушки.
        if (empty($result)) {
            return [
                'sentiment' => 'neutral',
                'suggestedReply' => "Здравствуйте, " . $dto->getName() . "! Спасибо за Ваше обращение. Я обязательно ознакомлюсь с ним и свяжусь с Вами в ближайшее время."
            ];
        }


        return $result;
    }
}
