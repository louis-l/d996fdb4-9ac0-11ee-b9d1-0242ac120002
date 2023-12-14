<?php

namespace App\ValueObjects;

use App\Services\Repositories\QuestionRepository;
use Illuminate\Support\Arr;

class StudentResponseAnswer
{
    public string $questionId;

    public string $response;

    public static function make(array $data): static
    {
        $instance = new static();
        $instance->questionId = Arr::get($data, 'questionId');
        $instance->response = Arr::get($data, 'response');

        return $instance;
    }

    public function toQuestionObject(): Question
    {
        if (! $questionBank = QuestionRepository::make()->find($this->questionId)) {
            throw new \RuntimeException('Could not find question');
        }

        return $questionBank;
    }
}
