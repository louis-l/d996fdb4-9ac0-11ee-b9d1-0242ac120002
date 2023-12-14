<?php

namespace App\ValueObjects;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class Assessment
{
    public string $id;

    public string $name;

    /**
     * @var \Illuminate\Support\Collection<array-key, \App\ValueObjects\AssessmentQuestion>
     */
    public Collection $questions;

    public static function make(array $data): static
    {
        $instance = new static();
        $instance->id = Arr::get($data, 'id');
        $instance->name = Arr::get($data, 'name');
        $instance->questions = collect(Arr::get($data, 'questions'))->map(fn (array $value) => AssessmentQuestion::make($value));

        return $instance;
    }
}
