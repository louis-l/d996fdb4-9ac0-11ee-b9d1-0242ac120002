<?php

namespace App\Services\Repositories;

use Closure;
use App\ValueObjects\StudentResponse;
use Illuminate\Support\Collection;

class StudentResponseRepository extends AbstractRepository
{
    /**
     * @param  string  $studentId
     * @return \Illuminate\Support\Collection<array-key, \App\ValueObjects\StudentResponse>
     */
    public function findResponsesFromStudentId(string $studentId): Collection
    {
        return $this->getDataCollection()
            ->filter(fn (StudentResponse $studentResponse) => $studentResponse->getStudentId() === $studentId);
    }

    protected function getDataSourceFile(): string
    {
        return 'student-responses.json';
    }

    protected function resolveDataMappingCallback(): Closure
    {
        return fn ($data) => StudentResponse::make($data);
    }
}
