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
