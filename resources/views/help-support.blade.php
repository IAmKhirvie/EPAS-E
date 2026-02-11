@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm mb-3">
                <i class="fas fa-arrow-left me-1"></i>Back
            </a>
            <h1 class="h3 mb-0">Help & Support</h1>
            <p class="text-muted">Find answers to common questions and get help using the system</p>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="row mb-4">
        <div class="col-md-3 col-6 mb-3">
            <div class="card border-0 shadow-sm h-100 text-center">
                <div class="card-body py-4">
                    <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-book text-primary fa-lg"></i>
                    </div>
                    <h6 class="mb-1">Getting Started</h6>
                    <small class="text-muted">Learn the basics</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="card border-0 shadow-sm h-100 text-center">
                <div class="card-body py-4">
                    <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-graduation-cap text-success fa-lg"></i>
                    </div>
                    <h6 class="mb-1">Courses</h6>
                    <small class="text-muted">How to take courses</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="card border-0 shadow-sm h-100 text-center">
                <div class="card-body py-4">
                    <div class="rounded-circle bg-warning bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-tasks text-warning fa-lg"></i>
                    </div>
                    <h6 class="mb-1">Assignments</h6>
                    <small class="text-muted">Submit your work</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="card border-0 shadow-sm h-100 text-center">
                <div class="card-body py-4">
                    <div class="rounded-circle bg-info bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-cog text-info fa-lg"></i>
                    </div>
                    <h6 class="mb-1">Account</h6>
                    <small class="text-muted">Manage settings</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- FAQ Section -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-question-circle text-primary me-2"></i>Frequently Asked Questions</h5>
                </div>
                <div class="card-body">
                    <div class="accordion" id="faqAccordion">
                        <!-- Getting Started -->
                        <div class="accordion-item border-0 mb-2">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed bg-light rounded" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    <strong>How do I access my courses?</strong>
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <p>To access your courses:</p>
                                    <ol>
                                        <li>Log in to your account</li>
                                        <li>Click on "Courses" in the sidebar menu</li>
                                        <li>Select the course you want to view</li>
                                        <li>Browse through the modules and information sheets</li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border-0 mb-2">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed bg-light rounded" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    <strong>How do I submit an assignment?</strong>
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <p>To submit an assignment:</p>
                                    <ol>
                                        <li>Navigate to the module containing the assignment</li>
                                        <li>Click on the assignment (Homework, Task Sheet, or Job Sheet)</li>
                                        <li>Complete all required fields</li>
                                        <li>Upload any required files</li>
                                        <li>Click the "Submit" button</li>
                                    </ol>
                                    <p class="text-muted small">Note: Some assignments have deadlines. Make sure to submit before the due date.</p>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border-0 mb-2">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed bg-light rounded" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    <strong>How do I take a Self-Check quiz?</strong>
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <p>Self-Check quizzes help you assess your understanding:</p>
                                    <ol>
                                        <li>After reading an Information Sheet, click on "Self-Check"</li>
                                        <li>Answer all the questions</li>
                                        <li>Click "Submit" to see your results</li>
                                        <li>Review correct answers and explanations</li>
                                    </ol>
                                    <p class="text-muted small">You can retake self-checks to improve your score.</p>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border-0 mb-2">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed bg-light rounded" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                    <strong>How do I check my grades?</strong>
                                </button>
                            </h2>
                            <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <p>To view your grades:</p>
                                    <ol>
                                        <li>Click on "Grades" in the sidebar menu</li>
                                        <li>View your overall progress and scores</li>
                                        <li>Click on individual courses for detailed breakdown</li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border-0 mb-2">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed bg-light rounded" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                                    <strong>How do I update my profile?</strong>
                                </button>
                            </h2>
                            <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <p>To update your profile:</p>
                                    <ol>
                                        <li>Click on your profile picture or name in the top right</li>
                                        <li>Select "Settings"</li>
                                        <li>Update your information in the Profile tab</li>
                                        <li>Click "Save Changes"</li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border-0 mb-2">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed bg-light rounded" type="button" data-bs-toggle="collapse" data-bs-target="#faq6">
                                    <strong>How do I change my password?</strong>
                                </button>
                            </h2>
                            <div id="faq6" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <p>To change your password:</p>
                                    <ol>
                                        <li>Go to Settings</li>
                                        <li>Click on the "Security" tab</li>
                                        <li>Enter your current password</li>
                                        <li>Enter and confirm your new password</li>
                                        <li>Click "Update Password"</li>
                                    </ol>
                                    <p class="text-muted small">For security, use a strong password with at least 8 characters.</p>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border-0 mb-2">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed bg-light rounded" type="button" data-bs-toggle="collapse" data-bs-target="#faq7">
                                    <strong>Can I download course materials?</strong>
                                </button>
                            </h2>
                            <div id="faq7" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <p>Yes! You can download course materials:</p>
                                    <ol>
                                        <li>Navigate to the module you want to download</li>
                                        <li>Click the "Download" or "Print" button</li>
                                        <li>The module will be generated as a PDF</li>
                                    </ol>
                                    <p class="text-muted small">Downloaded materials are also available offline if you install the app.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact & Support -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-headset text-success me-2"></i>Need More Help?</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Can't find what you're looking for? Contact your instructor or administrator.</p>

                    <div class="d-grid gap-2">
                        <a href="{{ route('contact') }}" class="btn btn-primary">
                            <i class="fas fa-envelope me-2"></i>Contact Support
                        </a>
                        <a href="{{ route('private.announcements.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-bullhorn me-2"></i>View Announcements
                        </a>
                    </div>
                </div>
            </div>


            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle text-warning me-2"></i>System Info</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0 small text-muted">
                        <li class="mb-2">
                            <i class="fas fa-code-branch me-2"></i>
                            Version: 1.0.0
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-server me-2"></i>
                            Status: <span class="text-success">Online</span>
                        </li>
                        <li>
                            <i class="fas fa-clock me-2"></i>
                            Last Updated: {{ now()->format('M d, Y') }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
