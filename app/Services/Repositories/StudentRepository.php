<?php

namespace App\Services\Repositories;

use App\ValueObjects\Student;
use Closure;

class StudentRepository extends AbstractRepository
{
    public function find(string $id): ?Student
    {
        return $this->getDataCollection()->firstWhere(fn (Student $student) => $student->id === $id);
    }

    protected function getDataSourceFile(): string
    {
        return 'students.json';
    }

    protected function resolveDataMappingCallback(): Closure
    {
        return fn ($data) => Student::make($data);
    }
}
