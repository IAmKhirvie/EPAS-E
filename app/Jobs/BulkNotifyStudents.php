<?php

namespace App\Jobs;

use App\Models\BulkOperationLog;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class BulkNotifyStudents implements ShouldQueue
{
    use Queueable;

    public $timeout = 600; // 10 minutes
    public $tries = 1;

    protected $operationId;
    protected $studentIds;
    protected $notificationType;
    protected $subject;
    protected $message;

    public function __construct(
        int $operationId,
        array $studentIds,
        string $notificationType,
        string $subject,
        string $message
    ) {
        $this->operationId = $operationId;
        $this->studentIds = $studentIds;
        $this->notificationType = $notificationType;
        $this->subject = $subject;
        $this->message = $message;
    }

    public function handle(): void
    {
        $operation = BulkOperationLog::find($this->operationId);

        if (!$operation) {
            Log::error("BulkNotifyStudents: Operation {$this->operationId} not found");
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
                    Log::info("BulkNotifyStudents: Operation {$this->operationId} was cancelled");
                    return;
                }
            }

            $operation->markAsCompleted();
            $operation->update([
                'results' => [
                    'notification_type' => $this->notificationType,
                    'subject' => $this->subject,
                    'notified_count' => $operation->successful_records,
                    'failed_count' => $operation->failed_records,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error("BulkNotifyStudents error: " . $e->getMessage());
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

                // Send notification based on type
                $this->sendNotification($student);
                $operation->incrementProgress(true);

            } catch (\Exception $e) {
                $operation->addError([
                    'student_id' => $studentId,
                    'email' => $student->email ?? 'unknown',
                    'message' => $e->getMessage(),
                ]);
                $operation->incrementProgress(false);
            }
        }
    }

    protected function sendNotification(User $student): void
    {
        switch ($this->notificationType) {
            case 'email':
                $this->sendEmailNotification($student);
                break;
            case 'database':
                $this->createDatabaseNotification($student);
                break;
            case 'both':
                $this->sendEmailNotification($student);
                $this->createDatabaseNotification($student);
                break;
            default:
                $this->createDatabaseNotification($student);
        }
    }

    protected function sendEmailNotification(User $student): void
    {
        // Simple email - can be replaced with a Mailable class
        Mail::raw($this->message, function ($mail) use ($student) {
            $mail->to($student->email)
                ->subject($this->subject);
        });
    }

    protected function createDatabaseNotification(User $student): void
    {
        // Store notification in database
        // This assumes you have a notifications table or use Laravel's notification system
        $student->notifications()->create([
            'type' => 'bulk_notification',
            'data' => [
                'subject' => $this->subject,
                'message' => $this->message,
            ],
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        $operation = BulkOperationLog::find($this->operationId);
        if ($operation) {
            $operation->markAsFailed($exception->getMessage());
        }
    }
}
