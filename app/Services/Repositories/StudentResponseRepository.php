<?php

namespace App\Services\Repositories;

use App\ValueObjects\StudentResponse;
use Illuminate\Support\Collection;

class StudentResponseRepository
{
    public static function make(): static
    {
        return resolve(static::class);
    }

    protected Collection $data;

    /**
     * @param  string  $studentId
     * @return \Illuminate\Support\Collection<array-key, \App\ValueObjects\StudentResponse>
     */
    public function findResponsesFromStudentId(string $studentId): Collection
    {
        return $this->getDataCollection()
            ->filter(fn (StudentResponse $studentResponse) => $studentResponse->getStudentId() === $studentId);
    }

    /**
     * @return \Illuminate\Support\Collection<array-key, \App\ValueObjects\Student>
     */
    protected function getDataCollection(): Collection
    {
        if (! isset($this->data)) {
            $this->data = $this->loadDataFromJsonFile(storage_path('data/student-responses.json'));
        }

        return $this->data;
    }

    protected function loadDataFromJsonFile(string $filePath): Collection
    {
        $fileContent = file_get_contents($filePath);

        return collect(json_decode($fileContent, true))
            ->map(fn ($data) => StudentResponse::make($data));
    }
}
