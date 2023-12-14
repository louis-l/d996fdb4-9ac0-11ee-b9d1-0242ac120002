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
    protected $signature = 'report {studentId?} {reportType?}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Generate student report';

    public function handle(): void
    {
        if (! $studentId = $this->argument('studentId')) {
            $studentId = $this->getStudentIdInput();
        }

        if (! $student = StudentRepository::make()->find($studentId)) {
            $this->error('Could not find student ID '.$studentId);
            return;
        }

        if (! $reportValue = $this->argument('reportType')) {
            $reportValue = $this->getReportTypeInput();
        }

        if (! $reportType = ReportType::tryFrom($reportValue)) {
            $this->error('Could not find report with value '.$reportValue);
            return;
        }

        $this->line(
            $this->generateReportFor($student, $reportType)
        );
    }

    protected function generateReportFor(Student $student, ReportType $reportType): string
    {
        return match ($reportType) {
            ReportType::Diagnostic => (new DiagnosticReport())->generate($student),
            ReportType::Progress => (new ProgressReport())->generate($student),
            ReportType::Feedback => (new FeedbackReport())->generate($student),
        };
    }

    protected function getStudentIdInput(): string
    {
        $prompt = new TextPrompt(
            label: 'Which student ID to generate?',
            required: true,
        );

        return $prompt->prompt();
    }

    protected function getReportTypeInput(): int
    {
        $prompt = new SelectPrompt(
            label: 'Report type',
            options: collect(ReportType::cases())->mapWithKeys(fn (ReportType $reportType) => [$reportType->value => $reportType->name]),
            required: true,
        );

        return (int) $prompt->prompt();
    }
}
