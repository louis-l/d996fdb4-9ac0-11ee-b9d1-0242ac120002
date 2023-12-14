<?php

namespace Tests\Unit\Services\ReportGenerator;

use App\Services\ReportGenerator\ProgressReport;
use App\Services\Repositories\AssessmentRepository;
use App\ValueObjects\Student;
use Tests\TestCase;

class ProgressReportTest extends TestCase
{
    public function test_file_exists(): void
    {
        $this->assertTrue(class_exists(ProgressReport::class));
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

        resolve(ProgressReport::class)->generate($student);
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

        resolve(ProgressReport::class)->generate($student);
    }
}
