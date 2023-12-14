<?php

namespace App\Services\Repositories;

use App\ValueObjects\Assessment;
use Illuminate\Support\Collection;

class AssessmentRepository
{
    public static function make(): static
    {
        return resolve(static::class);
    }

    protected Collection $data;

    public function find(string $id): ?Assessment
    {
        return $this->getDataCollection()->firstWhere(fn (Assessment $assessment) => $assessment->id === $id);
    }

    /**
     * @return \Illuminate\Support\Collection<array-key, \App\ValueObjects\Student>
     */
    protected function getDataCollection(): Collection
    {
        if (! isset($this->data)) {
            $this->data = $this->loadDataFromJsonFile(storage_path('data/assessments.json'));
        }

        return $this->data;
    }

    protected function loadDataFromJsonFile(string $filePath): Collection
    {
        $fileContent = file_get_contents($filePath);

        return collect(json_decode($fileContent, true))
            ->map(fn ($data) => Assessment::make($data));
    }
}
