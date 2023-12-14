<?php

namespace App\ValueObjects;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class StudentResponse
{
    protected array $data;

    public static function make(array $data): static
    {
        $instance = new static();
        $instance->data = $data;

        return $instance;
    }

    public function get($key, $default = null)
    {
        return Arr::get($this->data, $key, $default);
    }

    public function getAssessmentId(): string
    {
        return Arr::get($this->data, 'assessmentId');
    }

    public function getStudentId(): string
    {
        return Arr::get($this->data, 'student.id');
    }

    public function getScore(): int
    {
        return (int) Arr::get($this->data, 'results.rawScore');
    }

    public function countTotalAnswers(): int
    {
        return count(Arr::get($this->data, 'responses'));
    }

    public function hasAnsweredAllQuestionsCorrectly(): bool
    {
        return $this->getScore() === $this->countTotalAnswers();
    }

    public function getStartedDate(): Carbon
    {
        $rawDate = Arr::get($this->data, 'started');

        return Carbon::createFromFormat('d/m/Y H:i:s', $rawDate);
    }

    public function getAnswersCollection(): Collection
    {
        return collect($this->get('responses'))
            ->map(fn (array $data) => StudentResponseAnswer::make($data));
    }
}
