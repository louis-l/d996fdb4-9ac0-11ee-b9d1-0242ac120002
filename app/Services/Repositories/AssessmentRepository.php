<?php

namespace App\Services\Repositories;

use Closure;
use App\ValueObjects\Assessment;

class AssessmentRepository extends AbstractRepository
{
    public function find(string $id): ?Assessment
    {
        return $this->getDataCollection()->firstWhere(fn (Assessment $assessment) => $assessment->id === $id);
    }

    protected function getDataSourceFile(): string
    {
        return 'assessments.json';
    }

    protected function resolveDataMappingCallback(): Closure
    {
        return fn ($data) => Assessment::make($data);
    }
}
