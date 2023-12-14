<?php

namespace App\Services\ReportGenerator;

use App\Services\Repositories\AssessmentRepository;
use App\Services\Repositories\StudentResponseRepository;
use App\ValueObjects\Student;
use App\ValueObjects\StudentResponse;
use App\ValueObjects\StudentResponseAnswer;

class FeedbackReport
{
    public function generate(Student $student): string
    {
        $studentResponses = StudentResponseRepository::make()->findCompletedResponsesFromStudentId($student->id);

        if ($studentResponses->isEmpty()) {
            throw new \RuntimeException('Response not found.');
        }

        /** @var \App\ValueObjects\StudentResponse $lastStudentResponse */
        $lastStudentResponse = $studentResponses->last();

        $assessment = AssessmentRepository::make()->find($lastStudentResponse->getAssessmentId());

        if (! $assessment) {
            throw new \RuntimeException('Assessment not found.');
        }

        return implode(PHP_EOL, [
            sprintf(
                '%s %s recently completed %s assessment on %s',
                $student->firstName,
                $student->lastName,
                $assessment->name,
                $lastStudentResponse->getCompletedDate()->format('jS F Y H:i A'),
            ),
            sprintf(
                'He got %d questions right out of %d. %s',
                $lastStudentResponse->getScore(),
                $lastStudentResponse->countTotalAnswers(),
                $this->generateFeedbackSuffix($lastStudentResponse),
            ),
            '',
            $this->generateWrongAnswerFeedbackCollection($lastStudentResponse),
        ]);
    }

    protected function generateFeedbackSuffix(StudentResponse $studentResponse): string
    {
        if ($studentResponse->hasAnsweredAllQuestionsCorrectly()) {
            return 'No feedback for wrong answers.';
        }

        return 'Feedback for wrong answers given below';
    }

    protected function generateWrongAnswerFeedbackCollection(StudentResponse $studentResponse): string
    {
        return $studentResponse->getAnswersCollection()
            ->map(function (StudentResponseAnswer $answer) {
                $question = $answer->toQuestionObject();

                if ($question->isAnswerCorrect($answer->response)) {
                    return null;
                }

                if (! $answeredResponse = $question->findQuestionOptionById($answer->response)) {
                    throw new \RuntimeException('Could not find student answer response');
                }

                if (! $correctResponse = $question->findQuestionOptionById($question->config->key)) {
                    throw new \RuntimeException('Could not find correct answer response');
                }

                return implode(PHP_EOL, [
                    sprintf('Question: %s', $question->stem),
                    sprintf('Your answer: %s with value %s', $answeredResponse->label, $answeredResponse->value),
                    sprintf('Right answer: %s with value %s', $correctResponse->label, $correctResponse->value),
                    sprintf('Hint: %s', $question->config->hint),
                ]);
            })
            ->filter()
            ->join(PHP_EOL.PHP_EOL);
    }
}
