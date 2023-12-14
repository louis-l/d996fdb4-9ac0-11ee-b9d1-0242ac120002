<?php

namespace App\Services\ReportGenerator;

use App\Enums\ReportType;
use App\Services\Repositories\AssessmentRepository;
use App\Services\Repositories\QuestionRepository;
use App\Services\Repositories\StudentResponseRepository;
use App\ValueObjects\Student;
use App\ValueObjects\StudentResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class DiagnosticReport
{
    public function generate(Student $student, ReportType $reportType): string
    {
        $studentResponses = StudentResponseRepository::make()->findResponsesFromStudentId($student->id);

        if ($studentResponses->isEmpty()) {
            throw new \RuntimeException('Response not found.');
        }

        // TODO: Assume only reporting on 1st one
        $reportingResponse = $studentResponses->first();

        $assessment = AssessmentRepository::make()->find($reportingResponse->getAssessmentId());

        if (! $assessment) {
            throw new \RuntimeException('Assessment not found.');
        }

        return implode(PHP_EOL, [
            sprintf(
                '%s %s recently completed %s assessment on %s',
                $student->firstName,
                $student->lastName,
                $assessment->name,
                $reportingResponse->getStartedDate()->format('jS F Y H:i A'),
            ),
            sprintf(
                'He got %d questions right out of %d. Details by strand given below:',
                $reportingResponse->getScore(),
                $reportingResponse->countTotalAnswers(),
            ),
            ...$this->aaa($reportingResponse)
                ->map(fn (array $value, string $strand) => sprintf('%s: %d out of %d correct', $strand, $value['correctAnswers'], $value['totalQuestions']))
                ->toArray(),
        ]);
    }

    protected function aaa(StudentResponse $response): Collection
    {
        return collect($response->get('responses'))
            ->map(function (array $data) {
                $questionId = Arr::get($data, 'questionId');
                $providedAnswer = Arr::get($data, 'response');

                if (! $questionBank = QuestionRepository::make()->find($questionId)) {
                    throw new \RuntimeException('Could not find question');
                }

                return [
                    'strand' => $questionBank->strand,
                    'answer' => $providedAnswer,
                    'isCorrect' => $questionBank->isAnswerCorrect($providedAnswer),
                ];
            })
            ->groupBy('strand')
            ->map(fn (Collection $data, string $strand) => [
                'strand' => $strand,
                'correctAnswers' => $data->filter(fn (array $value) => $value['isCorrect'])->count(),
                'totalQuestions' => $data->count(),
            ]);
    }
}
