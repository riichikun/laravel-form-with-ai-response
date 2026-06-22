<?php

declare(strict_types=1);

namespace App\Services;

final readonly class FeedbackAiAnalysisDTO
{
    public function __construct(private string $comment, private string $name) {}

    public function getComment(): string
    {
        return $this->comment;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
