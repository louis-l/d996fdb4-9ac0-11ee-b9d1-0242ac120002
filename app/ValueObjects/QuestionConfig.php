<?php

namespace App\ValueObjects;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class QuestionConfig
{
    /**
     * @var \Illuminate\Support\Collection<array-key, \App\ValueObjects\QuestionOption>
     */
    public Collection $options;

    public string $key;

    public string $hint;

    public static function make(array $data): static
    {
        $instance = new static();
        $instance->key = Arr::get($data, 'key');
        $instance->hint = Arr::get($data, 'hint');
        $instance->options = collect(Arr::get($data, 'options'))->map(fn (array $value) => QuestionOption::make($value));

        return $instance;
    }
}
