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
            ['email' => 'Sample2@gmail.com'],
            [
                'password' => Hash::make('Password@123'),
                'first_name' => 'Sample2',
                'middle_name' => '',
                'last_name' => 'Sample2',
                'ext_name' => '',
                'role' => 'User',
                'department_id' => 1,
                'stat' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                
            ],
        );

        DB::table('users')->updateOrInsert(
            ['email' => 'khirvie@gmail.com'],
            [
                'password' => Hash::make('Password@123'),
                'first_name' => 'dads',
                'middle_name' => '',
                'last_name' => 'Sampdadale2',
                'ext_name' => '',
                'role' => 'User',
                'department_id' => 1,
                'stat' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                
            ],
        );

        DB::table('users')->updateOrInsert(
            ['email' => 'bast@gmail.com'],
            [
                'password' => Hash::make('Password@123'),
                'first_name' => 'wews',
                'middle_name' => '',
                'last_name' => 'Sampdadale2',
                'ext_name' => '',
                'role' => 'User',
                'department_id' => 1,
                'stat' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                
            ],
        );

        DB::table('users')->updateOrInsert(
            ['email' => 'bas2s@gmail.com'],
            [
                'password' => Hash::make('Password@123'),
                'first_name' => 'basts',
                'middle_name' => '',
                'last_name' => 'Sampdadale2',
                'ext_name' => '',
                'role' => 'User',
                'department_id' => 1,
                'stat' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                
            ],
        );
    }
}
