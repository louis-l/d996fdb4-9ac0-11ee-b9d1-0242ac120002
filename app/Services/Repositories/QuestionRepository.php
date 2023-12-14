<?php

namespace App\Services\Repositories;

use App\ValueObjects\Question;
use Illuminate\Support\Collection;

class QuestionRepository
{
    public static function make(): static
    {
        return resolve(static::class);
    }

    protected Collection $data;

    public function find(string $id): ?Question
    {
        return $this->getDataCollection()
            ->firstWhere(fn (Question $question) => $question->id === $id);
    }

    /**
     * @return \Illuminate\Support\Collection<array-key, \App\ValueObjects\Student>
     */
    protected function getDataCollection(): Collection
    {
        if (! isset($this->data)) {
            $this->data = $this->loadDataFromJsonFile(storage_path('data/questions.json'));
        }

        return $this->data;
    }

    protected function loadDataFromJsonFile(string $filePath): Collection
    {
        $fileContent = file_get_contents($filePath);

        return collect(json_decode($fileContent, true))
            ->map(fn ($data) => Question::make($data));
    }
}
