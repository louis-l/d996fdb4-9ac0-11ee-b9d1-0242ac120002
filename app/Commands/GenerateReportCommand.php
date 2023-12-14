<?php

namespace App\Commands;

use App\Enums\ReportType;
use App\Services\ReportGenerator\DiagnosticReport;
use App\Services\ReportGenerator\FeedbackReport;
use App\Services\ReportGenerator\ProgressReport;
use App\Services\Repositories\StudentRepository;
use App\ValueObjects\Student;
use Laravel\Prompts\SelectPrompt;
use Laravel\Prompts\TextPrompt;
use LaravelZero\Framework\Commands\Command;

class GenerateReportCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'report';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Generate student report';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->line('Please enter the following:');

        $this->line(
            $this->generateReportFor(
                $this->resolveStudent(),
                $this->getReportTypeInput(),
            )
        );
    }

    protected function generateReportFor(Student $student, ReportType $reportType)
    {
        return match ($reportType) {
            ReportType::Diagnostic => (new DiagnosticReport())->generate($student),
            ReportType::Progress => (new ProgressReport())->generate($student),
            ReportType::Feedback => (new FeedbackReport())->generate($student),
        };
    }

    protected function resolveStudent(): Student
    {
        $studentId = $this->getStudentIdInput();

        if ($student = StudentRepository::make()->find($studentId)) {
            return $student;
        }

        $this->error('Could not find student ID '.$studentId);
        return $this->resolveStudent();
    }

    protected function getStudentIdInput(): string
    {
        $prompt = new TextPrompt(
            label: 'Which student ID to generate?',
            required: true,
        );

        return $prompt->prompt();
    }

    protected function getReportTypeInput(): ReportType
    {
        $prompt = new SelectPrompt(
            label: 'Report type',
            options: collect(ReportType::cases())->mapWithKeys(fn (ReportType $reportType) => [$reportType->value => $reportType->name]),
            required: true,
        );

        $selectedValue = $prompt->prompt();

        return Reporttype::tryFrom($selectedValue);
    }
}
