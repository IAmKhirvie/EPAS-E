<?php

namespace App\Services;

use App\Models\User;
use App\Models\Notification;
use App\Models\Homework;
use App\Models\ForumThread;
use App\Models\Conversation;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    protected PHPMailerService $mailer;

    public function __construct(PHPMailerService $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Send notification for homework submission.
     */
    public function notifyHomeworkSubmitted(User $instructor, User $student, Homework $homework): void
    {
        $this->createNotification($instructor, 'homework_submitted', [
            'title' => 'New Homework Submission',
            'message' => "{$student->full_name} has submitted homework: {$homework->title}",
            'homework_id' => $homework->id,
            'student_id' => $student->id,
        ]);

        if ($instructor->getNotificationPreference('email_homework_submitted', true)) {
            $this->sendEmail($instructor, 'New Homework Submission',
                "{$student->full_name} has submitted their homework: {$homework->title}. Please review it at your earliest convenience.");
        }
    }

    /**
     * Send notification for grade posted.
     */
    public function notifyGradePosted(User $student, string $itemType, string $itemTitle, float $score, float $maxScore): void
    {
        $percentage = round(($score / $maxScore) * 100, 1);

        $this->createNotification($student, 'grade_posted', [
            'title' => 'Grade Posted',
            'message' => "Your {$itemType} \"{$itemTitle}\" has been graded: {$score}/{$maxScore} ({$percentage}%)",
            'item_type' => $itemType,
            'item_title' => $itemTitle,
            'score' => $score,
            'max_score' => $maxScore,
        ]);

        if ($student->getNotificationPreference('email_grade_posted', true)) {
            $this->sendEmail($student, 'Grade Posted',
                "Your {$itemType} \"{$itemTitle}\" has been graded.\n\nScore: {$score}/{$maxScore} ({$percentage}%)");
        }
    }

    /**
     * Send notification for upcoming deadline.
     */
    public function notifyDeadlineReminder(User $student, string $itemType, string $itemTitle, string $dueDate): void
    {
        $this->createNotification($student, 'deadline_reminder', [
            'title' => 'Deadline Reminder',
            'message' => "Reminder: {$itemType} \"{$itemTitle}\" is due on {$dueDate}",
            'item_type' => $itemType,
            'item_title' => $itemTitle,
            'due_date' => $dueDate,
        ]);

        if ($student->getNotificationPreference('email_deadline_reminder', true)) {
            $this->sendEmail($student, 'Deadline Reminder',
                "This is a reminder that your {$itemType} \"{$itemTitle}\" is due on {$dueDate}.\n\nPlease make sure to submit it before the deadline.");
        }
    }

    /**
     * Send notification for forum reply.
     */
    public function notifyForumReply(User $user, ForumThread $thread, User $replier): void
    {
        if ($user->id === $replier->id) {
            return; // Don't notify user of their own reply
        }

        $this->createNotification($user, 'forum_reply', [
            'title' => 'New Forum Reply',
            'message' => "{$replier->full_name} replied to your thread: {$thread->title}",
            'thread_id' => $thread->id,
            'replier_id' => $replier->id,
        ]);

        if ($user->getNotificationPreference('email_forum_reply', true)) {
            $this->sendEmail($user, 'New Reply to Your Thread',
                "{$replier->full_name} has replied to your forum thread: \"{$thread->title}\".\n\nCheck it out to see what they said!");
        }
    }

    /**
     * Send notification for new message.
     */
    public function notifyNewMessage(User $recipient, User $sender, Conversation $conversation): void
    {
        $this->createNotification($recipient, 'new_message', [
            'title' => 'New Message',
            'message' => "You have a new message from {$sender->full_name}",
            'conversation_id' => $conversation->id,
            'sender_id' => $sender->id,
        ]);

        if ($recipient->getNotificationPreference('email_new_message', true)) {
            $this->sendEmail($recipient, 'New Message',
                "You have received a new message from {$sender->full_name}.\n\nLog in to view and reply to the message.");
        }
    }

    /**
     * Send notification for new announcement.
     */
    public function notifyAnnouncement(User $user, string $title, bool $isUrgent = false): void
    {
        $this->createNotification($user, 'announcement', [
            'title' => $isUrgent ? 'Urgent Announcement' : 'New Announcement',
            'message' => $title,
            'is_urgent' => $isUrgent,
        ]);

        if ($user->getNotificationPreference('email_announcement', true)) {
            $urgentPrefix = $isUrgent ? '[URGENT] ' : '';
            $this->sendEmail($user, "{$urgentPrefix}New Announcement",
                "A new announcement has been posted: \"{$title}\"\n\nLog in to read the full announcement.");
        }
    }

    /**
     * Send notification for new student registration (to admins/instructors).
     */
    public function notifyNewRegistration(string $studentName, string $studentEmail): void
    {
        $admins = User::whereIn('role', ['admin', 'instructor'])->where('stat', 1)->get();

        foreach ($admins as $admin) {
            $this->createNotification($admin, 'new_registration', [
                'title' => 'New Student Registration',
                'message' => "New student registration: {$studentName} ({$studentEmail}). Awaiting email verification and admin approval.",
                'student_name' => $studentName,
                'student_email' => $studentEmail,
            ]);
        }
    }

    /**
     * Send notification when student verifies their email (to admins/instructors).
     */
    public function notifyRegistrationVerified(string $studentName, string $studentEmail): void
    {
        $admins = User::whereIn('role', ['admin', 'instructor'])->where('stat', 1)->get();

        foreach ($admins as $admin) {
            $this->createNotification($admin, 'registration_verified', [
                'title' => 'Email Verified - Awaiting Approval',
                'message' => "Student {$studentName} ({$studentEmail}) has verified their email. Ready for admin approval.",
                'student_name' => $studentName,
                'student_email' => $studentEmail,
            ]);
        }
    }

    /**
     * Send notification for new enrollment request (to admins/instructors).
     */
    public function notifyEnrollmentRequest(User $student, string $courseName): void
    {
        $admins = User::whereIn('role', ['admin', 'instructor'])->where('stat', 1)->get();

        foreach ($admins as $admin) {
            $this->createNotification($admin, 'enrollment_request', [
                'title' => 'New Enrollment Request',
                'message' => "{$student->full_name} requested to enroll in {$courseName}.",
                'student_id' => $student->id,
                'course_name' => $courseName,
            ]);
        }
    }

    /**
     * Send notification when student account is approved (to student).
     */
    public function notifyAccountApproved(User $student): void
    {
        $this->createNotification($student, 'account_approved', [
            'title' => 'Account Approved!',
            'message' => 'Congratulations! Your account has been approved. You can now access all features of EPAS-E LMS.',
        ]);

        if ($student->getNotificationPreference('email_account_status', true)) {
            $this->sendEmail($student, 'Your Account Has Been Approved',
                "Congratulations! Your EPAS-E LMS account has been approved.\n\nYou now have full access to the learning management system. Log in to start learning!");
        }
    }

    /**
     * Send notification when student account is rejected (to student).
     */
    public function notifyAccountRejected(User $student, ?string $reason = null): void
    {
        $message = 'Your account registration has been rejected.';
        if ($reason) {
            $message .= " Reason: {$reason}";
        }

        $this->createNotification($student, 'account_rejected', [
            'title' => 'Registration Rejected',
            'message' => $message,
            'reason' => $reason,
        ]);

        if ($student->getNotificationPreference('email_account_status', true)) {
            $emailBody = "Unfortunately, your EPAS-E LMS registration has been rejected.";
            if ($reason) {
                $emailBody .= "\n\nReason: {$reason}";
            }
            $emailBody .= "\n\nIf you believe this was a mistake, please contact the administrator.";

            $this->sendEmail($student, 'Registration Update', $emailBody);
        }
    }

    /**
     * Create an in-app notification.
     */
    protected function createNotification(User $user, string $type, array $data): Notification
    {
        return Notification::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $data['title'] ?? 'Notification',
            'message' => $data['message'] ?? '',
            'data' => $data,
        ]);
    }

    /**
     * Send an email notification.
     */
    protected function sendEmail(User $user, string $subject, string $body): bool
    {
        try {
            return $this->mailer->sendMail(
                $user->email,
                $user->full_name,
                $subject,
                $this->formatEmailBody($body),
                $body
            );
        } catch (\Exception $e) {
            \Log::error("Failed to send email notification: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Format the email body with HTML template.
     */
    protected function formatEmailBody(string $content): string
    {
        return "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #007fc9; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f9f9f9; }
                .footer { padding: 20px; text-align: center; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>JOMS LMS</h1>
                </div>
                <div class='content'>
                    " . nl2br(htmlspecialchars($content)) . "
                </div>
                <div class='footer'>
                    <p>This is an automated notification from JOMS LMS.</p>
                    <p>You can manage your notification preferences in your account settings.</p>
                </div>
            </div>
        </body>
        </html>";
    }

    /**
     * Get unread notification count for a user.
     */
    public function getUnreadCount(User $user): int
    {
        return Notification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->count();
    }

    /**
     * Mark all notifications as read for a user.
     */
    public function markAllAsRead(User $user): void
    {
        Notification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    /**
     * Get recent notifications for a user.
     */
    public function getRecent(User $user, int $limit = 10)
    {
        return Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
