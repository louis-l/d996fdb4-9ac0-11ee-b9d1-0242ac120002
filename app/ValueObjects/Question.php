<?php

namespace App\ValueObjects;

use Illuminate\Support\Arr;

class Question
{
    public string $id;

    public string $stem;

    public string $type;

    public string $strand;

    public QuestionConfig $config;

    public static function make(array $data): static
    {
        $instance = new static();
        $instance->id = Arr::get($data, 'id');
        $instance->stem = Arr::get($data, 'stem');
        $instance->type = Arr::get($data, 'type');
        $instance->strand = Arr::get($data, 'strand');
        $instance->config = QuestionConfig::make(Arr::get($data, 'config'));

        return $instance;
    }

    public function isAnswerCorrect(string $answer): bool
    {
        return $this->config->key === $answer;
    }

    public function findQuestionOptionById(string $id): ?QuestionOption
    {
        return $this->config->options->firstWhere(fn (QuestionOption $questionOption) => $questionOption->id === $id);
    }
}
