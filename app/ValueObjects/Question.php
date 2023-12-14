<?php

namespace App\ValueObjects;

use Illuminate\Support\Arr;

class Question
{
    public string $id;

    public string $stem;

    public string $type;

    public string $strand;

    public array $config;

    public static function make(array $data): static
    {
        $instance = new static();
        $instance->id = Arr::get($data, 'id');
        $instance->stem = Arr::get($data, 'stem');
        $instance->type = Arr::get($data, 'type');
        $instance->strand = Arr::get($data, 'strand');
        $instance->config = Arr::get($data, 'config');

        return $instance;
    }

    public function isAnswerCorrect(string $answer): bool
    {
        return Arr::get($this->config, 'key') === $answer;
    }
}
