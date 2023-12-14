<?php

namespace App\ValueObjects;

use Illuminate\Support\Arr;

class AssessmentQuestion
{
    public string $questionId;

    public int $position;

    public static function make(array $data): static
    {
        $instance = new static();
        $instance->questionId = Arr::get($data, 'questionId');
        $instance->position = Arr::get($data, 'position');

        return $instance;
    }
}
