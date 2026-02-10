<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file contains all web routes for the application organized by feature.
| Routes are grouped logically with proper middleware, prefixes, and names.
|
| Route Groups:
| 1. Public Routes (no authentication required)
| 2. Authentication Routes (login, register, password reset)
| 3. Email Verification Routes
| 4. Protected Routes (requires authentication)
|    - Dashboard Routes
|    - User Management Routes
|    - Content Management Routes
|    - Assessment Routes
|    - Class Management Routes
|    - Announcements Routes
|    - Grades & Certificates Routes
|    - Analytics Routes
|    - Settings & Profile Routes
|    - Security Routes (2FA, Audit Logs)
|
*/

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;

// Controllers
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PrivateLoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentDashboard;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\ModuleContentController;
use App\Http\Controllers\InformationSheetController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\SelfCheckController;
use App\Http\Controllers\TaskSheetController;
use App\Http\Controllers\JobSheetController;
use App\Http\Controllers\HomeworkController;
use App\Http\Controllers\PerformanceCriteriaController;
use App\Http\Controllers\ChecklistController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\EnrollmentRequestController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\GradesController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\TwoFactorController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ContactController;

/*
|--------------------------------------------------------------------------
| Root Route & Fallback
|--------------------------------------------------------------------------
|
| The root route redirects authenticated users to their dashboard,
| and unauthenticated users to the lobby page.
|
*/

Route::get('/', function () {
    if (Auth::check()) {
        return app(DashboardController::class)->redirectToRoleDashboard();
    }
    return redirect()->route('lobby');
});

Route::fallback(function () {
    if (request()->expectsJson() || request()->ajax()) {
        return response()->json([
            'error' => true,
            'message' => 'The page you are looking for could not be found.',
            'status' => 404,
        ], 404);
    }

    if (Auth::check()) {
        return redirect()->route('dashboard')
            ->with('error_popup', 'The page you were looking for could not be found.')
            ->with('error_code', 404);
    }
    return redirect()->route('lobby');
});

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
|
| These routes are accessible without authentication.
| Includes the lobby, about page, and contact form.
|
*/

Route::get('/lobby', function () {
    return view('lobby');
})->name('lobby');

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');

Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');

// Public certificate verification (allows anyone to verify a certificate's authenticity)
Route::post('/verify-certificate', [CertificateController::class, 'verify'])->name('certificates.verify');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
|
| Routes for user authentication including login, registration,
| password reset, and logout functionality.
|
*/

// Student Authentication
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->middleware('throttle:3,1');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Password Reset
Route::prefix('forgot-password')->group(function () {
    Route::get('/', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email')->middleware('throttle:5,1');
});

Route::prefix('reset-password')->group(function () {
    Route::get('/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/', [ForgotPasswordController::class, 'reset'])->name('password.update')->middleware('throttle:5,1');
});

// Registration Verification (public routes for email verification flow)
Route::get('/verify-registration/{token}', [RegisterController::class, 'verifyEmail'])->name('registration.verify');
Route::post('/registration/resend', [RegisterController::class, 'resendVerification'])->name('registration.resend');
Route::post('/registration/status', [RegisterController::class, 'checkStatus'])->name('registration.status');

// Admin Authentication
Route::prefix('admin')->group(function () {
    Route::get('/login', [PrivateLoginController::class, 'showAdminLoginForm'])->name('admin.login');
    Route::post('/login', [PrivateLoginController::class, 'adminLogin'])->name('admin.login.submit');
});

// Instructor Authentication
Route::prefix('instructor')->group(function () {
    Route::get('/login', [PrivateLoginController::class, 'showInstructorLoginForm'])->name('instructor.login');
    Route::post('/login', [PrivateLoginController::class, 'instructorLogin'])->name('instructor.login.submit');
});

// Legacy Private Login (backward compatibility - redirects to admin login)
Route::get('/private/login', function () {
    return redirect()->route('admin.login');
})->name('private.login');

/*
|--------------------------------------------------------------------------
| Email Verification Routes
|--------------------------------------------------------------------------
|
| Routes for handling email verification for registered users.
| Includes verification notice, verification link handler, and resend.
|
*/

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware(['auth'])->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
    $user = \App\Models\User::findOrFail($id);

    // Verify the hash matches the user's email
    if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        return redirect('/login')->withErrors(['email' => 'Invalid verification link.']);
    }

    // Check if already verified
    if ($user->hasVerifiedEmail()) {
        return redirect('/login')->with('status', 'Email already verified. You can login.');
    }

    // Mark email as verified
    $user->markEmailAsVerified();

    return redirect('/login')->with('status', 'Email verified successfully! You can now login.');
})->middleware(['signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $user = $request->user();

    if ($user->hasVerifiedEmail()) {
        $message = 'Your email is already verified.';
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $message]);
        }
        return back()->with('status', $message);
    }

    try {
        $mailer = new \App\Services\PHPMailerService();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->getKey(),
                'hash' => sha1($user->getEmailForVerification()),
            ]
        );

        $result = $mailer->sendVerificationEmail($user, $verificationUrl);

        if ($result) {
            $message = 'Verification email sent! Please check your inbox.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true, 'message' => $message]);
            }
            return back()->with('status', $message);
        }

        $error = 'Failed to send verification email. Please try again.';
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => false, 'message' => $error], 500);
        }
        return back()->withErrors(['email' => $error]);
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error("Failed to send verification email: " . $e->getMessage());
        $error = 'An error occurred while sending the email. Please check your mail configuration.';
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => false, 'message' => $error], 500);
        }
        return back()->withErrors(['email' => $error]);
    }
})->middleware(['auth', 'throttle:3,1'])->name('verification.send');

/*
|--------------------------------------------------------------------------
| Two-Factor Authentication Challenge
|--------------------------------------------------------------------------
|
| Routes for the 2FA challenge flow during login.
| Separated from main auth middleware group for the challenge process.
|
*/

Route::middleware(['auth'])->group(function () {
    Route::get('/two-factor/challenge', [TwoFactorController::class, 'challenge'])->name('two-factor.challenge');
    Route::post('/two-factor/verify', [TwoFactorController::class, 'verify'])->name('two-factor.verify');
});

/*
|--------------------------------------------------------------------------
| Protected Routes (Requires Authentication)
|--------------------------------------------------------------------------
|
| All routes below require the user to be authenticated.
| Routes are organized by feature area with appropriate middleware.
|
*/

Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard Routes
    |--------------------------------------------------------------------------
    |
    | Role-based dashboard routing for students, instructors, and admins.
    | Each role has their own dashboard view and data endpoints.
    |
    */

    // Main dashboard router (redirects based on user role)
    Route::get('/dashboard', [DashboardController::class, 'redirectToRoleDashboard'])->name('dashboard');

    // Student Dashboard
    Route::prefix('student')->name('student.')->group(function () {
        Route::get('/dashboard', [StudentDashboard::class, 'index'])->name('dashboard');
        Route::get('/dashboard-data', [DashboardController::class, 'getStudentDashboardData'])->name('dashboard-data');
        Route::get('/progress-data', [StudentDashboard::class, 'getProgressData'])->name('progress-data');
    });

    // Admin Dashboard
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard-data', [DashboardController::class, 'getAdminDashboardData']);
    });

    // Instructor Dashboard Data
    Route::get('/instructor/dashboard-data', [DashboardController::class, 'getInstructorDashboardData']);

    // Shared Dashboard Progress Endpoints
    Route::get('/dashboard/progress-data', [DashboardController::class, 'getProgressData']);
    Route::get('/dashboard/progress-report', [DashboardController::class, 'getProgressReport']);

    /*
    |--------------------------------------------------------------------------
    | User Management Routes
    |--------------------------------------------------------------------------
    |
    | Routes for managing users (students, instructors, admins).
    | Includes CRUD operations, bulk actions, and role-specific views.
    | Access restricted to admin and instructor roles.
    |
    */

    Route::middleware(['check.role:admin,instructor'])->group(function () {

        // User CRUD Operations
        Route::prefix('private/users')->name('private.users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/create', [UserController::class, 'create'])->name('create');
            Route::post('/', [UserController::class, 'store'])->name('store');
            Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
            Route::put('/{user}', [UserController::class, 'update'])->name('update');
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
            Route::post('/{user}/approve', [UserController::class, 'approve'])->name('approve');

            // Bulk User Actions
            Route::post('/bulk-delete', [UserController::class, 'bulkDelete'])->name('bulk-delete');
            Route::post('/bulk-activate', [UserController::class, 'bulkActivate'])->name('bulk-activate');
            Route::post('/bulk-deactivate', [UserController::class, 'bulkDeactivate'])->name('bulk-deactivate');
            Route::post('/bulk-assign-section', [UserController::class, 'bulkAssignSection'])->name('bulk-assign-section');

            // Bulk Import
            Route::get('/import', [UserController::class, 'showImportForm'])->name('import');
            Route::post('/import', [UserController::class, 'processImport'])->name('import.process');
            Route::get('/import/template', [UserController::class, 'downloadTemplate'])->name('import.template');
        });

        // Role-Specific User Lists
        Route::get('/private/students', [UserController::class, 'students'])->name('private.students.index');
        Route::get('/private/instructors', [UserController::class, 'instructors'])->name('private.instructors.index');
        Route::get('/private/admins', [UserController::class, 'admins'])->name('private.admins.index');
        Route::post('/private/students/{student}/remove-from-class', [UserController::class, 'removeFromClass'])->name('private.students.remove-from-class');

        // Registration Management (approve/reject pending registrations)
        Route::prefix('admin/registrations')->name('admin.registrations.')->group(function () {
            Route::get('/', [RegistrationController::class, 'index'])->name('index');
            // Bulk actions (must be before wildcard routes)
            Route::post('/bulk-approve', [RegistrationController::class, 'bulkApprove'])->name('bulk-approve');
            Route::post('/bulk-reject', [RegistrationController::class, 'bulkReject'])->name('bulk-reject');
            // Individual registration actions
            Route::get('/{registration}', [RegistrationController::class, 'show'])->name('show');
            Route::post('/{registration}/approve', [RegistrationController::class, 'approve'])->name('approve');
            Route::post('/{registration}/reject', [RegistrationController::class, 'reject'])->name('reject');
            Route::post('/{registration}/resend', [RegistrationController::class, 'resendVerification'])->name('resend');
            Route::delete('/{registration}', [RegistrationController::class, 'destroy'])->name('destroy');
        });
    });

    // Instructor Index (accessible to authenticated users)
    Route::get('/instructor', [UserController::class, 'instructor'])->name('instructor.index');

    /*
    |--------------------------------------------------------------------------
    | Content Management Routes
    |--------------------------------------------------------------------------
    |
    | Routes for managing educational content including courses, modules,
    | information sheets, topics, and various assessment types.
    |
    */

    // Content Management Dashboard (Admin/Instructor only)
    Route::middleware(['check.role:admin,instructor'])->group(function () {
        Route::get('/content-management', [CourseController::class, 'contentManagement'])->name('content.management');

        // Course Management
        Route::resource('courses', CourseController::class)->except(['index', 'show']);
        Route::post('/courses/{course}/assign-instructor', [CourseController::class, 'assignInstructor'])
            ->name('courses.assign-instructor')
            ->middleware('check.role:admin');

        // Module Management
        Route::resource('modules', ModuleController::class)->except(['index', 'show']);
        Route::post('/modules/{module}/upload-image', [ModuleController::class, 'uploadImage'])->name('modules.upload-image');
        Route::delete('/modules/{module}/images/{image}', [ModuleController::class, 'deleteImage'])->name('modules.delete-image');

        // Information Sheet Management
        Route::prefix('modules/{module}')->group(function () {
            Route::get('/information-sheets/create', [InformationSheetController::class, 'create'])->name('information-sheets.create');
            Route::post('/information-sheets', [InformationSheetController::class, 'store'])->name('information-sheets.store');
            Route::get('/information-sheets/{informationSheet}/edit', [InformationSheetController::class, 'edit'])->name('information-sheets.edit');
            Route::put('/information-sheets/{informationSheet}', [InformationSheetController::class, 'update'])->name('information-sheets.update');
            Route::delete('/information-sheets/{informationSheet}', [InformationSheetController::class, 'destroy'])->name('information-sheets.destroy');
            Route::get('/information-sheets/{informationSheet}/download', [InformationSheetController::class, 'download'])->name('information-sheets.download');
        });

        // Topic Management
        Route::prefix('information-sheets/{informationSheet}')->group(function () {
            Route::get('/topics/create', [TopicController::class, 'create'])->name('topics.create');
            Route::post('/topics', [TopicController::class, 'store'])->name('topics.store');
            Route::get('/topics/{topic}/edit', [TopicController::class, 'edit'])->name('topics.edit');
            Route::put('/topics/{topic}', [TopicController::class, 'update'])->name('topics.update');
        });
        Route::delete('/topics/{topic}', [TopicController::class, 'destroy'])->name('topics.destroy');
    });

    // Content View Routes (All authenticated users)
    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');
    Route::get('/modules', [ModuleController::class, 'index'])->name('modules.index');
    Route::get('/modules/{module}', [ModuleController::class, 'show'])->name('modules.show');
    Route::get('/modules/{module}/download', [ModuleController::class, 'downloadPdf'])->name('modules.download');
    Route::get('/modules/{module}/print', [ModuleController::class, 'printPreview'])->name('modules.print');
    Route::get('/modules/{module}/progress', [ModuleController::class, 'getModuleProgress'])->name('modules.progress');

    // Information Sheet View Routes
    Route::prefix('modules/{module}')->group(function () {
        Route::get('/information-sheets/{informationSheet}', [ModuleController::class, 'showInformationSheet'])->name('information-sheets.show');
    });

    // Information Sheet Content API (AJAX loading)
    Route::get('/modules/information-sheets/{informationSheet}/content', [ModuleController::class, 'getSheetContent'])->name('information-sheets.content');

    // Topic Content Routes
    Route::get('/topics/{topic}/content', [TopicController::class, 'getContent'])->name('topics.content');
    Route::get('/modules/information-sheets/{informationSheet}/topics/{topic}', [TopicController::class, 'getTopicContent'])->name('information-sheets.topics.content');
    Route::get('/modules/{module}/information-sheets/{informationSheet}/topics/{topic}', [ModuleController::class, 'showTopic'])->name('modules.topics.show');

    // Module Content API
    Route::get('/module-content/{module}/{contentType}', [ModuleContentController::class, 'show'])->name('module-content.show');
    Route::get('/api/module-content/{module}/{contentType}', [ModuleContentController::class, 'getContentApi'])->name('api.module-content.show');

    /*
    |--------------------------------------------------------------------------
    | Assessment Routes
    |--------------------------------------------------------------------------
    |
    | Routes for managing and taking assessments including self-checks,
    | task sheets, job sheets, homework, and competency checklists.
    |
    */

    // Assessment Management (Admin/Instructor only)
    Route::middleware(['check.role:admin,instructor'])->group(function () {

        // Self Check Management
        Route::prefix('information-sheets/{informationSheet}')->group(function () {
            Route::get('/self-checks/create', [SelfCheckController::class, 'create'])->name('self-checks.create');
            Route::post('/self-checks', [SelfCheckController::class, 'store'])->name('self-checks.store');
            Route::get('/self-checks/{selfCheck}/edit', [SelfCheckController::class, 'edit'])->name('self-checks.edit');
            Route::put('/self-checks/{selfCheck}', [SelfCheckController::class, 'update'])->name('self-checks.update');
            Route::delete('/self-checks/{selfCheck}', [SelfCheckController::class, 'destroy'])->name('self-checks.destroy');
        });

        // Quiz Media Upload
        Route::post('/quiz/upload-image', [SelfCheckController::class, 'uploadImage'])->name('quiz.upload-image');
        Route::post('/quiz/upload-audio', [SelfCheckController::class, 'uploadAudio'])->name('quiz.upload-audio');
        Route::post('/quiz/upload-video', [SelfCheckController::class, 'uploadVideo'])->name('quiz.upload-video');

        // Task Sheet Management
        Route::prefix('information-sheets/{informationSheet}')->group(function () {
            Route::get('/task-sheets/create', [TaskSheetController::class, 'create'])->name('task-sheets.create');
            Route::post('/task-sheets', [TaskSheetController::class, 'store'])->name('task-sheets.store');
            Route::get('/task-sheets/{taskSheet}/edit', [TaskSheetController::class, 'edit'])->name('task-sheets.edit');
            Route::put('/task-sheets/{taskSheet}', [TaskSheetController::class, 'update'])->name('task-sheets.update');
            Route::delete('/task-sheets/{taskSheet}', [TaskSheetController::class, 'destroy'])->name('task-sheets.destroy');
        });

        // Job Sheet Management
        Route::prefix('information-sheets/{informationSheet}')->group(function () {
            Route::get('/job-sheets/create', [JobSheetController::class, 'create'])->name('job-sheets.create');
            Route::post('/job-sheets', [JobSheetController::class, 'store'])->name('job-sheets.store');
            Route::get('/job-sheets/{jobSheet}/edit', [JobSheetController::class, 'edit'])->name('job-sheets.edit');
            Route::put('/job-sheets/{jobSheet}', [JobSheetController::class, 'update'])->name('job-sheets.update');
            Route::delete('/job-sheets/{jobSheet}', [JobSheetController::class, 'destroy'])->name('job-sheets.destroy');
        });

        // Homework Management
        Route::prefix('information-sheets/{informationSheet}')->group(function () {
            Route::get('/homeworks/create', [HomeworkController::class, 'create'])->name('homeworks.create');
            Route::post('/homeworks', [HomeworkController::class, 'store'])->name('homeworks.store');
            Route::get('/homeworks/{homework}/edit', [HomeworkController::class, 'edit'])->name('homeworks.edit');
            Route::put('/homeworks/{homework}', [HomeworkController::class, 'update'])->name('homeworks.update');
            Route::delete('/homeworks/{homework}', [HomeworkController::class, 'destroy'])->name('homeworks.destroy');
        });

        // Performance Criteria Management
        Route::prefix('performance-criteria')->name('performance-criteria.')->group(function () {
            Route::get('/create', [PerformanceCriteriaController::class, 'create'])->name('create');
            Route::post('/', [PerformanceCriteriaController::class, 'store'])->name('store');
            Route::get('/{performanceCriteria}/edit', [PerformanceCriteriaController::class, 'edit'])->name('edit');
            Route::put('/{performanceCriteria}', [PerformanceCriteriaController::class, 'update'])->name('update');
            Route::delete('/{performanceCriteria}', [PerformanceCriteriaController::class, 'destroy'])->name('destroy');
        });

        // Checklist Management
        Route::prefix('information-sheets/{informationSheet}')->group(function () {
            Route::get('/checklists/create', [ChecklistController::class, 'create'])->name('checklists.create');
            Route::post('/checklists', [ChecklistController::class, 'store'])->name('checklists.store');
            Route::get('/checklists/{checklist}/edit', [ChecklistController::class, 'edit'])->name('checklists.edit');
            Route::put('/checklists/{checklist}', [ChecklistController::class, 'update'])->name('checklists.update');
            Route::delete('/checklists/{checklist}', [ChecklistController::class, 'destroy'])->name('checklists.destroy');
        });
    });

    // Assessment View & Submit Routes (All authenticated users)

    // Self Checks
    Route::get('/self-checks/{selfCheck}', [SelfCheckController::class, 'show'])->name('self-checks.show');
    Route::post('/self-checks/{selfCheck}/submit', [SelfCheckController::class, 'submit'])->name('self-checks.submit');
    Route::get('/modules/{module}/information-sheets/{informationSheet}/self-check', [SelfCheckController::class, 'showBySheet'])->name('modules.information-sheets.self-check');

    // Task Sheets
    Route::get('/task-sheets/{taskSheet}', [TaskSheetController::class, 'show'])->name('task-sheets.show');
    Route::post('/task-sheets/{taskSheet}/submit', [TaskSheetController::class, 'submit'])->name('task-sheets.submit');

    // Job Sheets
    Route::get('/job-sheets/{jobSheet}', [JobSheetController::class, 'show'])->name('job-sheets.show');
    Route::post('/job-sheets/{jobSheet}/submit', [JobSheetController::class, 'submit'])->name('job-sheets.submit');

    // Homework
    Route::get('/homeworks/{homework}', [HomeworkController::class, 'show'])->name('homeworks.show');
    Route::post('/homeworks/{homework}/submit', [HomeworkController::class, 'submit'])->name('homeworks.submit');

    // Checklists
    Route::get('/checklists/{checklist}', [ChecklistController::class, 'show'])->name('checklists.show');
    Route::post('/checklists/{checklist}/evaluate', [ChecklistController::class, 'evaluate'])->name('checklists.evaluate');

    /*
    |--------------------------------------------------------------------------
    | Class Management Routes
    |--------------------------------------------------------------------------
    |
    | Routes for managing classes/sections and enrollment requests.
    | Admin and instructors can manage class assignments and enrollments.
    |
    */

    Route::middleware(['check.role:admin,instructor'])->group(function () {

        // Class/Section Management
        Route::prefix('class-management')->name('class-management.')->group(function () {
            Route::get('/', [ClassController::class, 'index'])->name('index');
            Route::get('/{section}', [ClassController::class, 'show'])->name('show');
            Route::post('/{section}/assign-adviser', [ClassController::class, 'assignAdviser'])->name('assign-adviser');
            Route::delete('/{section}/remove-adviser', [ClassController::class, 'removeAdviser'])->name('remove-adviser');
        });

        // Enrollment Requests Management
        Route::prefix('enrollment-requests')->name('enrollment-requests.')->group(function () {
            Route::get('/', [EnrollmentRequestController::class, 'index'])->name('index');
            Route::get('/create', [EnrollmentRequestController::class, 'create'])->name('create');
            Route::post('/', [EnrollmentRequestController::class, 'store'])->name('store');
            Route::post('/{enrollmentRequest}/approve', [EnrollmentRequestController::class, 'approve'])->name('approve');
            Route::post('/{enrollmentRequest}/reject', [EnrollmentRequestController::class, 'reject'])->name('reject');
            Route::delete('/{enrollmentRequest}', [EnrollmentRequestController::class, 'cancel'])->name('cancel');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Announcement Routes
    |--------------------------------------------------------------------------
    |
    | Routes for creating and viewing announcements.
    | Admin/Instructors can create; all users can view and interact.
    |
    */

    // Announcement Management (Admin/Instructor only)
    Route::middleware(['check.role:admin,instructor'])->group(function () {
        Route::get('/announcements/create', [AnnouncementController::class, 'create'])->name('private.announcements.create');
        Route::post('/announcements', [AnnouncementController::class, 'store'])->name('private.announcements.store');
    });

    // Announcement View & Interaction (All authenticated users)
    Route::prefix('announcements')->group(function () {
        Route::get('/', [AnnouncementController::class, 'index'])->name('private.announcements.index');
        Route::get('/{announcement}', [AnnouncementController::class, 'show'])->name('private.announcements.show');
        Route::post('/{announcement}/comment', [AnnouncementController::class, 'addComment'])->name('private.announcements.comment');
    });

    // Announcement API Endpoints
    Route::prefix('api/announcements')->name('api.announcements.')->group(function () {
        Route::get('/recent', [AnnouncementController::class, 'getRecentAnnouncements'])->name('recent');
    });

    /*
    |--------------------------------------------------------------------------
    | Grades & Certificates Routes
    |--------------------------------------------------------------------------
    |
    | Routes for viewing grades and managing/downloading certificates.
    | Students can view their own; admin/instructors can view all and export.
    |
    */

    // Grades
    Route::prefix('grades')->name('grades.')->group(function () {
        Route::get('/', [GradesController::class, 'index'])->name('index');
        Route::get('/api/my-grades', [GradesController::class, 'getStudentGradesApi'])->name('api.my-grades');
        Route::get('/export', [GradesController::class, 'exportGrades'])->name('export')->middleware('check.role:admin,instructor');
        Route::get('/export-class/{section}', [GradesController::class, 'exportClassGrades'])->name('export-class')->middleware('check.role:admin,instructor');
        Route::get('/{student}', [GradesController::class, 'show'])->name('show')->where('student', '[0-9]+');
    });

    // Certificates
    Route::prefix('certificates')->name('certificates.')->group(function () {
        Route::get('/', [CertificateController::class, 'index'])->name('index');
        Route::get('/{certificate}', [CertificateController::class, 'show'])->name('show');
        Route::get('/{certificate}/download', [CertificateController::class, 'download'])->name('download');
        Route::post('/course/{course}/generate', [CertificateController::class, 'generate'])->name('generate');
    });

    /*
    |--------------------------------------------------------------------------
    | Analytics Routes
    |--------------------------------------------------------------------------
    |
    | Routes for viewing analytics and reports.
    | Access restricted to admin and instructor roles.
    |
    */

    Route::prefix('analytics')->name('analytics.')->middleware('check.role:admin,instructor')->group(function () {
        Route::get('/', [AnalyticsController::class, 'dashboard'])->name('dashboard');
        Route::get('/users', [AnalyticsController::class, 'users'])->name('users');
        Route::get('/courses', [AnalyticsController::class, 'courses'])->name('courses');

        // Analytics API Endpoints
        Route::get('/api/metrics', [AnalyticsController::class, 'getMetricsApi'])->name('api.metrics');
        Route::get('/api/top-performers', [AnalyticsController::class, 'topPerformers'])->name('api.top-performers');
        Route::get('/api/at-risk', [AnalyticsController::class, 'atRiskStudents'])->name('api.at-risk');

        // Analytics Export
        Route::get('/export/students', [AnalyticsController::class, 'exportStudentProgress'])->name('export.students');
        Route::get('/export/pdf', [AnalyticsController::class, 'exportPdfReport'])->name('export.pdf');

        // Cache Management
        Route::post('/refresh-cache', [AnalyticsController::class, 'refreshCache'])->name('refresh-cache');
    });

    /*
    |--------------------------------------------------------------------------
    | Audit Log Routes
    |--------------------------------------------------------------------------
    |
    | Routes for viewing system audit logs.
    | Access restricted to admin role only.
    |
    */

    Route::prefix('audit-logs')->name('audit-logs.')->middleware('check.role:admin')->group(function () {
        Route::get('/', [AuditLogController::class, 'index'])->name('index');
        Route::get('/security', [AuditLogController::class, 'security'])->name('security');
        Route::get('/export', [AuditLogController::class, 'export'])->name('export');
        Route::get('/{auditLog}', [AuditLogController::class, 'show'])->name('show');
    });

    /*
    |--------------------------------------------------------------------------
    | Two-Factor Authentication Management Routes
    |--------------------------------------------------------------------------
    |
    | Routes for setting up and managing two-factor authentication.
    |
    */

    Route::prefix('two-factor')->name('two-factor.')->group(function () {
        Route::get('/setup', [TwoFactorController::class, 'setup'])->name('setup');
        Route::post('/enable', [TwoFactorController::class, 'enable'])->name('enable');
        Route::get('/manage', [TwoFactorController::class, 'manage'])->name('manage');
        Route::delete('/disable', [TwoFactorController::class, 'disable'])->name('disable');
        Route::get('/backup-codes', [TwoFactorController::class, 'regenerateBackupCodes'])->name('backup-codes');
    });

    /*
    |--------------------------------------------------------------------------
    | Profile & Settings Routes
    |--------------------------------------------------------------------------
    |
    | Routes for managing user profile and application settings.
    |
    */

    // Profile
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');

    // Profile Image Serving (serves uploaded profile images)
    Route::get('/storage/profile-images/{filename}', function ($filename) {
        $filename = basename($filename);
        $path = storage_path('app/public/profile-images/' . $filename);

        if (!file_exists($path)) {
            abort(404);
        }

        return response()->file($path);
    })->where('filename', '[a-zA-Z0-9_\-\.]+')->name('profile.image');

    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        Route::post('/profile', [SettingsController::class, 'updateProfile'])->name('profile');
        Route::post('/profile-picture', [SettingsController::class, 'updateProfilePicture'])->name('profile-picture');
        Route::post('/password', [SettingsController::class, 'updatePassword'])->name('password');
        Route::post('/notifications', [SettingsController::class, 'updateNotifications'])->name('notifications');
        Route::post('/appearance', [SettingsController::class, 'updateAppearance'])->name('appearance');
        Route::post('/privacy', [SettingsController::class, 'updatePrivacy'])->name('privacy');
        Route::post('/system', [SettingsController::class, 'updateSystem'])->name('system')->middleware('check.role:admin');
        Route::get('/export', [SettingsController::class, 'exportData'])->name('export');
        Route::post('/delete-account', [SettingsController::class, 'deleteAccount'])->name('delete-account');
        Route::post('/resend-verification', [SettingsController::class, 'resendVerification'])->name('resend-verification');
    });

    /*
    |--------------------------------------------------------------------------
    | Help & Support Routes
    |--------------------------------------------------------------------------
    |
    | Routes for help and support pages.
    |
    */

    Route::get('/help-support', function () {
        return view('help-support');
    })->name('help-support');

});
