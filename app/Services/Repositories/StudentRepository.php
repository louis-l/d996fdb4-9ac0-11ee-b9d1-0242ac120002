<?php

namespace App\Services\Repositories;

use App\ValueObjects\Student;
use Illuminate\Support\Collection;

class StudentRepository
{
    public static function make(): static
    {
        return resolve(static::class);
    }

    /**
     * @var \Illuminate\Support\Collection<array-key, \App\ValueObjects\Student>
     */
    protected Collection $data;

    public function find(string $id): ?Student
    {
        return $this->getDataCollection()->firstWhere(fn (Student $student) => $student->id === $id);
    }

    /**
     * @return \Illuminate\Support\Collection<array-key, \App\ValueObjects\Student>
     */
    protected function getDataCollection(): Collection
    {
        if (! isset($this->data)) {
            $this->data = $this->loadDataFromJsonFile(storage_path('data/students.json'));
        }

        return $this->data;
    }

    protected function loadDataFromJsonFile(string $filePath): Collection
    {
        $fileContent = file_get_contents($filePath);

        return collect(json_decode($fileContent, true))
            ->map(fn ($data) => Student::make($data));
    }
}
