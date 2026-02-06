<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Grading Configuration
    |--------------------------------------------------------------------------
    */

    'grading' => [
        'default_passing_score' => 70,
        'homework_pass_threshold' => 0.6,
        'module_min_score_threshold' => 0.7,
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication & Security
    |--------------------------------------------------------------------------
    */

    'auth' => [
        'email_change_cooldown_hours' => 24,
        'verification_token_minutes' => 60,
        'reset_token_hours' => 1,
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Policy
    |--------------------------------------------------------------------------
    */

    'password' => [
        'min_length' => 8,
        'require_uppercase' => true,
        'require_lowercase' => true,
        'require_numeric' => true,
        'require_special' => true,
        // Regex pattern for validation (must match all requirements above)
        'regex' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#^()_+=\-\[\]{}|:;<>,.?\/~`])[A-Za-z\d@$!%*?&#^()_+=\-\[\]{}|:;<>,.?\/~`]{8,}$/',
        'message' => 'Password must be at least 8 characters and contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    */

    'rate_limits' => [
        'login_attempts' => 5,
        'login_decay_minutes' => 15,
        'registration_per_minute' => 3,
        'password_reset_per_minute' => 5,
        'verification_resend_per_minute' => 3,
    ],

    /*
    |--------------------------------------------------------------------------
    | Session Security
    |--------------------------------------------------------------------------
    */

    'session' => [
        'absolute_timeout_minutes' => 480, // 8 hours
        'idle_timeout_minutes' => 30,
        'regenerate_on_login' => true,
        'invalidate_on_logout' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Gamification Points
    |--------------------------------------------------------------------------
    */

    'gamification' => [
        'points' => [
            'topic_complete' => 10,
            'self_check_pass' => 25,
            'homework_submit' => 15,
            'perfect_score' => 50,
            'daily_login' => 5,
            'module_complete' => 100,
            'course_complete' => 500,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    */

    'pagination' => [
        'default' => 15,
        'users' => 20,
        'audit_logs' => 25,
    ],

];
