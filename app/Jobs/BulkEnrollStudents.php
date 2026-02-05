<?php

namespace App\Jobs;

use App\Models\BulkOperationLog;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class BulkEnrollStudents implements ShouldQueue
{
    use Queueable;

    public $timeout = 600; // 10 minutes
    public $tries = 1;

    protected $operationId;
    protected $studentIds;
    protected $section;

    public function __construct(int $operationId, array $studentIds, string $section)
    {
        $this->operationId = $operationId;
        $this->studentIds = $studentIds;
        $this->section = $section;
    }

    public function handle(): void
    {
        $operation = BulkOperationLog::find($this->operationId);

        if (!$operation) {
            Log::error("BulkEnrollStudents: Operation {$this->operationId} not found");
            return;
        }

        $operation->markAsProcessing();

        try {
            // Process in chunks for scalability
            $chunks = array_chunk($this->studentIds, 50);

            foreach ($chunks as $chunk) {
                $this->processChunk($operation, $chunk);

                // Check if operation was cancelled
                $operation->refresh();
                if ($operation->status === 'cancelled') {
                    Log::info("BulkEnrollStudents: Operation {$this->operationId} was cancelled");
                    return;
                }
            }

            $operation->markAsCompleted();
            $operation->update([
                'results' => [
                    'section' => $this->section,
                    'enrolled_count' => $operation->successful_records,
                    'failed_count' => $operation->failed_records,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error("BulkEnrollStudents error: " . $e->getMessage());
            $operation->markAsFailed($e->getMessage());
        }
    }

    protected function processChunk(BulkOperationLog $operation, array $studentIds): void
    {
        foreach ($studentIds as $studentId) {
            try {
                $student = User::where('id', $studentId)
                    ->where('role', 'student')
                    ->first();

                if (!$student) {
                    $operation->addError([
                        'student_id' => $studentId,
                        'message' => 'Student not found',
                    ]);
                    $operation->incrementProgress(false);
                    continue;
                }

                $student->update(['section' => $this->section]);
                $operation->incrementProgress(true);

            } catch (\Exception $e) {
                $operation->addError([
                    'student_id' => $studentId,
                    'message' => $e->getMessage(),
                ]);
                $operation->incrementProgress(false);
            }
        }
    }

    public function failed(\Throwable $exception): void
    {
        $operation = BulkOperationLog::find($this->operationId);
        if ($operation) {
            $operation->markAsFailed($exception->getMessage());
        }
    }
}
