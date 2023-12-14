<?php

namespace Tests\Unit\Services\ReportGenerator;

use App\Services\ReportGenerator\FeedbackReport;
use App\Services\Repositories\AssessmentRepository;
use App\ValueObjects\Student;
use Tests\TestCase;

class FeedbackReportTest extends TestCase
{
    public function test_file_exists(): void
    {
        $this->assertTrue(class_exists(FeedbackReport::class));
    }

    public function test_generating_report_for_student_that_does_not_have_assessment_responses(): void
    {
        $student = Student::make([
            'id' => '1',
            'firstName' => 'First',
            'lastName' => 'Last',
            'yearLevel' => 1,
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Response not found.');

        resolve(FeedbackReport::class)->generate($student);
    }

    public function test_generating_report_for_student_that_does_not_have_assessment(): void
    {
        $student = Student::make([
            'id' => 'student1',
            'firstName' => 'First',
            'lastName' => 'Last',
            'yearLevel' => 1,
        ]);

        $this->partialMock(AssessmentRepository::class)
            ->allows('find')
            ->once()
            ->andReturn(null);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Assessment not found.');

        resolve(FeedbackReport::class)->generate($student);
    }
}
