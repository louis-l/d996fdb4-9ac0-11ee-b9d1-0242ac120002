<?php

namespace App\Services\ReportGenerator;

use App\Services\Repositories\AssessmentRepository;
use App\Services\Repositories\StudentResponseRepository;
use App\ValueObjects\Student;
use App\ValueObjects\StudentResponse;
use App\ValueObjects\StudentResponseAnswer;
use Illuminate\Support\Collection;

class DiagnosticReport
{
    public function generate(Student $student): string
    {
        $studentResponses = StudentResponseRepository::make()->findCompletedResponsesFromStudentId($student->id);

        if ($studentResponses->isEmpty()) {
            throw new \RuntimeException('Response not found.');
        }

        // TODO: Assume only reporting on latest assessment
        $reportingResponse = $studentResponses->last();

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
                $reportingResponse->getCompletedDate()->format('jS F Y H:i A'),
            ),
            sprintf(
                'He got %d questions right out of %d. Details by strand given below:',
                $reportingResponse->getScore(),
                $reportingResponse->countTotalAnswers(),
            ),
            '',
            ...$this->groupAnswers($reportingResponse)
                ->map(fn (array $value, string $strand) => sprintf('%s: %d out of %d correct', $strand, $value['correctAnswers'], $value['totalQuestions']))
                ->toArray(),
        ]);
    }

    protected function groupAnswers(StudentResponse $response): Collection
    {
        return $response->getAnswersCollection()
            ->map(function (StudentResponseAnswer $answer) {
                $question = $answer->toQuestionObject();

                return [
                    'strand' => $question->strand,
                    'answer' => $answer->response,
                    'isCorrect' => $question->isAnswerCorrect($answer->response),
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
