<?php

namespace App\ValueObjects;

use Illuminate\Support\Arr;

class Student
{
    public string $id;

    public string $firstName;

    public string $lastName;

    public int $yearLevel;

    public static function make(array $data): static
    {
        $instance = new static();
        $instance->id = Arr::get($data, 'id');
        $instance->firstName = Arr::get($data, 'firstName');
        $instance->lastName = Arr::get($data, 'lastName');
        $instance->yearLevel = Arr::get($data, 'yearLevel');

        return $instance;
    }
}
