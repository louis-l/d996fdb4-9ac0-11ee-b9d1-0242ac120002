<?php

namespace App\Services\ReportGenerator;

use App\Services\Repositories\AssessmentRepository;
use App\Services\Repositories\StudentResponseRepository;
use App\ValueObjects\Student;
use App\ValueObjects\StudentResponse;
use Illuminate\Support\Collection;

class ProgressReport
{
    public function generate(Student $student): string
    {
        $studentResponses = StudentResponseRepository::make()->findResponsesFromStudentId($student->id);

        if ($studentResponses->isEmpty()) {
            throw new \RuntimeException('Response not found.');
        }

        $attemptedAssessments = $studentResponses->map(fn (StudentResponse $response) => $response->getAssessmentId());

        // TODO: Assume each student can only take 1 assessment
        if ($attemptedAssessments->unique()->count() >= 2) {
            throw new \RuntimeException('Student has taken more than 1 assessment.');
        }

        $assessment = AssessmentRepository::make()->find($attemptedAssessments->first());

        $progressFeedback = $this->generateProgressFeedback($studentResponses);

        return implode(PHP_EOL, [
            sprintf(
                '%s %s has completed %s assessment %d times in total. Date and raw score given below:',
                $student->firstName,
                $student->lastName,
                $assessment->name,
                $attemptedAssessments->count(),
            ),
            '',
            ...$studentResponses
                ->map(fn (StudentResponse $studentResponse) => sprintf(
                    'Date: %s, Raw Score: %d out of %d',
                    $studentResponse->getStartedDate()->format('jS F Y'),
                    $studentResponse->getScore(),
                    $studentResponse->countTotalAnswers(),
                ))
                ->toArray(),
            '',
            $progressFeedback
                ? sprintf('%s %s %s', $student->firstName, $student->lastName, $progressFeedback)
                : null,
        ]);
    }

    protected function generateProgressFeedback(Collection $studentResponses): string
    {
        // To compare, we need at least 2 attempts
        if ($studentResponses->count() <= 1) {
            return '';
        }

        /** @var \App\ValueObjects\StudentResponse $lastAttempt */
        $lastAttempt = $studentResponses->last();
        /** @var \App\ValueObjects\StudentResponse $secondLastAttempt */
        $secondLastAttempt = $studentResponses->skip($studentResponses->count() - 2)->first();

        $countMoreCorrectAnswer = $lastAttempt->getScore() - $secondLastAttempt->getScore();

        if ($countMoreCorrectAnswer >= 1) {
            return sprintf(
                'got %d more correct in the recent completed assessment than the oldest',
                $countMoreCorrectAnswer,
            );
        }

        if ($lastAttempt->getScore() === $lastAttempt->countTotalAnswers()) {
            return 'got all answers correct in the recent completed assessment';
        }

        return '';
    }
}
