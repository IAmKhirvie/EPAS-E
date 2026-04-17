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
        DB::table('users')->updateOrInsert(
            ['email' => 'khirviecliffordbautista@gmail.com'],
            [
                'student_id' => 'MAR000000000001',
                'password' => Hash::make('EPASe@2025'),
                'first_name' => 'Khirvie Clifford',
                'middle_name' => 'N.',
                'last_name' => 'Bautista',
                'ext_name' => '',
                'role' => 'admin',
                'department_id' => 1,
                'stat' => 1,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
