<?php

namespace App\ValueObjects;

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
}
