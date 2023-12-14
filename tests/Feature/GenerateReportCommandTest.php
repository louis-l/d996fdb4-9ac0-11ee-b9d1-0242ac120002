<?php

namespace Tests\Feature;

use App\Commands\GenerateReportCommand;
use Tests\TestCase;

class GenerateReportCommandTest extends TestCase
{
    public function test_command_exists(): void
    {
        $this->assertTrue(class_exists(GenerateReportCommand::class));
    }

    public function test_generate_report_using_wrong_student_id(): void
    {
        $this
            ->artisan('report studentIdDoesNotExist')
            ->expectsOutput('Could not find student ID studentIdDoesNotExist');
    }

    public function test_generates_diagnostic_report_using_wrong_report_type(): void
    {
        $this
            ->artisan('report student1 4')
            ->expectsOutput('Could not find report with value 4');
    }

    public function test_generates_diagnostic_report_successfully(): void
    {
        $this
            ->artisan('report student1 1')
            ->expectsOutput(trim(file_get_contents(base_path('tests/stubs/1-1.txt'))));
    }

    public function test_generates_progress_report_successfully(): void
    {
        $this
            ->artisan('report student1 2')
            ->expectsOutput(trim(file_get_contents(base_path('tests/stubs/1-2.txt'))));
    }

    public function test_generates_feedback_report_successfully(): void
    {
        $this
            ->artisan('report student1 3')
            ->expectsOutput(trim(file_get_contents(base_path('tests/stubs/1-3.txt'))));
    }
}
