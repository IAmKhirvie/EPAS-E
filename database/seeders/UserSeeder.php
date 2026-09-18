<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin
        DB::table('users')->updateOrInsert(
            ['email' => 'admin@hasa.test'],
            [
                'student_id' => 'MAR000000000001',
                'password' => Hash::make('ChangeMe@2026'),
                'first_name' => 'Admin',
                'middle_name' => '',
                'last_name' => 'User',
                'ext_name' => '',
                'role' => 'admin',
                'department_id' => 1,
                'stat' => 1,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Instructor 1
        DB::table('users')->updateOrInsert(
            ['email' => 'instructor1@hasa.test'],
            [
                'student_id' => 'MAR000000000002',
                'password' => Hash::make('ChangeMe@2026'),
                'first_name' => 'Instructor',
                'middle_name' => '',
                'last_name' => 'One',
                'ext_name' => '',
                'role' => 'instructor',
                'section' => 'GROUP-B',
                'department_id' => 1,
                'stat' => 1,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Student 1 (GROUP-A)
        DB::table('users')->updateOrInsert(
            ['email' => 'student1@hasa.test'],
            [
                'student_id' => 'MAR000000000003',
                'password' => Hash::make('ChangeMe@2026'),
                'first_name' => 'Student',
                'middle_name' => 'A',
                'last_name' => 'One',
                'ext_name' => '',
                'role' => 'student',
                'section' => 'GROUP-A',
                'department_id' => 1,
                'stat' => 1,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Student 2 (GROUP-A)
        DB::table('users')->updateOrInsert(
            ['email' => 'student2@hasa.test'],
            [
                'student_id' => 'MAR000000000004',
                'password' => Hash::make('ChangeMe@2026'),
                'first_name' => 'Student',
                'middle_name' => 'B',
                'last_name' => 'Two',
                'ext_name' => '',
                'role' => 'student',
                'section' => 'GROUP-A',
                'department_id' => 1,
                'stat' => 1,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Student 3 (GROUP-B)
        DB::table('users')->updateOrInsert(
            ['email' => 'student3@hasa.test'],
            [
                'student_id' => 'MAR000000000005',
                'password' => Hash::make('ChangeMe@2026'),
                'first_name' => 'Student',
                'middle_name' => 'C',
                'last_name' => 'Three',
                'ext_name' => '',
                'role' => 'student',
                'section' => 'GROUP-B',
                'department_id' => 1,
                'stat' => 1,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Instructor 2 (GROUP-A)
        DB::table('users')->updateOrInsert(
            ['email' => 'instructor2@hasa.test'],
            [
                'student_id' => 'MAR000000000006',
                'password' => Hash::make('ChangeMe@2026'),
                'first_name' => 'Instructor',
                'middle_name' => '',
                'last_name' => 'Two',
                'ext_name' => '',
                'role' => 'instructor',
                'section' => 'GROUP-A',
                'department_id' => 1,
                'stat' => 1,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}