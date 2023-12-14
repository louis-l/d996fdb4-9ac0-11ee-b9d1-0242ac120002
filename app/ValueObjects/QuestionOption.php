<?php

namespace App\ValueObjects;

use Illuminate\Support\Arr;

class QuestionOption
{
    public string $id;

    public string $label;

    public string $value;

    public static function make(array $data): static
    {
        $instance = new static();
        $instance->id = Arr::get($data, 'id');
        $instance->label = Arr::get($data, 'label');
        $instance->value = Arr::get($data, 'value');

        return $instance;
    }
}
