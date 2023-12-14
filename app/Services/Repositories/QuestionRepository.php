<?php

namespace App\Services\Repositories;

use Closure;
use App\ValueObjects\Question;

class QuestionRepository extends AbstractRepository
{
    public function find(string $id): ?Question
    {
        return $this->getDataCollection()
            ->firstWhere(fn (Question $question) => $question->id === $id);
    }

    protected function getDataSourceFile(): string
    {
        return 'questions.json';
    }

    protected function resolveDataMappingCallback(): Closure
    {
        return fn ($data) => Question::make($data);
    }
}
