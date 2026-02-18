<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $firstNames = [
            'Khirvie', 'Karl', 'Sheila', 'Jerry', 'Mikaella', 'Kooky', 'Angel', 'Angelo', 'Kevin', 'Trisha',
            'Nathan', 'Lance', 'Erika', 'Hannah', 'Joshua', 'Nicole', 'Jessa', 'Elijah', 'Christian', 'Samantha',
            'Miguel', 'Alyssa', 'Jared', 'Franz', 'Rhea', 'Andrea', 'Dominic', 'Shane', 'Carlo', 'Bianca',
            'Paolo', 'Denise', 'Lorenzo', 'Faye', 'Mara', 'Renz', 'Ella', 'Dale', 'Arvin', 'Bea',
            'Harvey', 'Grace', 'Liam', 'Macy', 'Jayden', 'Clara', 'Reina', 'Evan', 'Kyle', 'Nina'
        ];

        $lastNames = [
            'Bautista', 'Rapada', 'Merida', 'Reyes', 'Torre', 'Arabia', 'Poblete', 'Pascual', 'Sy', 'Lopez',
            'Garcia', 'Cruz', 'DelaCruz', 'Santos', 'Torres', 'Villanueva', 'Navarro', 'Flores', 'Reyes', 'Ramos',
            'Castro', 'Aquino', 'Domingo', 'Diaz', 'Fernandez', 'Marquez', 'Rodriguez', 'Mendoza', 'Gonzales', 'Vergara',
            'Velasquez', 'Salazar', 'Lim', 'Chan', 'Chua', 'Go', 'Tan', 'Cortez', 'Manalo', 'Ocampo',
            'Rivera', 'Soriano', 'Villamor', 'Yap', 'Cabrera', 'Vergara', 'Reyes', 'Santiago', 'Gutierrez', 'Francisco'
        ];

        $sections = ['A1', 'B1', 'C1', 'D1', 'E1', 'F1'];
        $roles = ['student', 'instructor']; // You can include 'admin' if you want

        $users = [];

        for ($i = 0; $i < 50; $i++) {
            $firstName = $firstNames[$i];
            $lastName = $lastNames[$i];
            $email = strtolower($firstName . $lastName . mt_rand(100, 999) . '@gmail.com');
            $section = $sections[array_rand($sections)];
            $role = $roles[array_rand($roles)];

            $users[] = [
                'student_id' => 'MAR' . str_pad(mt_rand(1, 999999999999), 12, '0', STR_PAD_LEFT),
                'email' => $email,
                'password' => Hash::make('Password@123'),
                'first_name' => $firstName,
                'middle_name' => Str::upper(chr(rand(65, 90))) . '.',
                'last_name' => $lastName,
                'ext_name' => '',
                'role' => $role,
                'section' => $section, // <-- add this field in your users table if not yet existing
                'department_id' => 1,
                'stat' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('users')->insert($users);


        DB::table('users')->updateOrInsert(
            ['email' => 'Juswa@gmail.com'],
            [
                'student_id' => 'MAR' . str_pad(mt_rand(1, 999999999999), 12, '0', STR_PAD_LEFT),
                'password' => Hash::make('Password123'),
                'first_name' => 'Khirvie Clifford',
                'middle_name' => 'N.',
                'last_name' => 'Bautista',
                'ext_name' => '',
                'role' => 'admin',
                'department_id' => 1,
                'stat' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('users')->updateOrInsert(
            ['email' => 'karl142412@gmail.com'],
            [
                'student_id' => 'MAR' . str_pad(mt_rand(1, 999999999999), 12, '0', STR_PAD_LEFT),
                'password' => Hash::make('Password@123'),
                'first_name' => 'Karl Lynuz',
                'middle_name' => 'B.',
                'last_name' => 'Rapada',
                'ext_name' => '',
                'role' => 'instructor', 
                'department_id' => 1,
                'stat' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ] 
        );

        DB::table('users')->updateOrInsert(
            ['email' => 'Sheila1112421152@gmail.com'],
            [
                'student_id' => 'MAR' . str_pad(mt_rand(1, 999999999999), 12, '0', STR_PAD_LEFT),
                'password' => Hash::make('Password@123'),
                'first_name' => 'Sheila Marie',
                'middle_name' => 'M.',
                'last_name' => 'Merida',
                'ext_name' => '',
                'role' => 'student',
                'department_id' => 1,
                'stat' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        DB::table('users')->updateOrInsert(
            ['email' => 'Jerry152511@gmail.com'],
            [
                'student_id' => 'MAR' . str_pad(mt_rand(1, 999999999999), 12, '0', STR_PAD_LEFT),
                'password' => Hash::make('Password@123'),
                'first_name' => 'Jerry',
                'middle_name' => 'A.',
                'last_name' => 'Reyes',
                'ext_name' => '',
                'role' => 'student',
                'department_id' => 1,
                'stat' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        DB::table('users')->updateOrInsert(
            ['email' => 'mikaellayap2513152@gmail.com'],
            [
                'student_id' => 'MAR' . str_pad(mt_rand(1, 999999999999), 12, '0', STR_PAD_LEFT),
                'password' => Hash::make('Password@123'),
                'first_name' => 'Mikaella Rosalia',
                'middle_name' => 'Y.',
                'last_name' => 'Torre',
                'ext_name' => '',
                'role' => 'student',
                'department_id' => 1,
                'stat' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        DB::table('users')->updateOrInsert(
            ['email' => 'kookyl51yan112@gmail.com'],
            [
                'student_id' => 'MAR' . str_pad(mt_rand(1, 999999999999), 12, '0', STR_PAD_LEFT),
                'password' => Hash::make('Password@123'),
                'first_name' => 'Kooky Lyann',
                'middle_name' => 'A.',
                'last_name' => 'Arabia',
                'ext_name' => '',
                'role' => 'student',
                'department_id' => 1,
                'stat' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        DB::table('users')->updateOrInsert(
            ['email' => 'khirviecliffordbautista15132@gmail.com'],
            [
                'student_id' => 'MAR' . str_pad(mt_rand(1, 999999999999), 12, '0', STR_PAD_LEFT),
                'password' => Hash::make('Password@123'),
                'first_name' => 'Khirvie Clifford',
                'middle_name' => 'N.',
                'last_name' => 'Bautista',
                'ext_name' => '',
                'role' => 'student',
                'department_id' => 1,
                'stat' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        DB::table('users')->updateOrInsert(
            ['email' => 'mikaellayap23@gmail.com'],
            [
                'student_id' => 'MAR' . str_pad(mt_rand(1, 999999999999), 12, '0', STR_PAD_LEFT),
                'password' => Hash::make('Password123'),
                'first_name' => 'Mikaella',
                'middle_name' => 'Y.',
                'last_name' => 'Yap',
                'ext_name' => '',
                'role' => 'student',
                'department_id' => 1,
                'stat' => 1,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        DB::table('users')->updateOrInsert(
            ['email' => 'AngelLov251e31@gmail.com'],
            [
                'student_id' => 'MAR' . str_pad(mt_rand(1, 999999999999), 12, '0', STR_PAD_LEFT),
                'password' => Hash::make('Password@123'),
                'first_name' => 'Angel Love',
                'middle_name' => 'D.',
                'last_name' => 'Poblete',
                'ext_name' => '',
                'role' => 'student',
                'department_id' => 1,
                'stat' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        DB::table('users')->updateOrInsert(
            ['email' => 'AngeloPascual112513@gmail.com'],
            [
                'student_id' => 'MAR' . str_pad(mt_rand(1, 999999999999), 12, '0', STR_PAD_LEFT),
                'password' => Hash::make('Password@123'),
                'first_name' => 'Angelo',
                'middle_name' => 'A.',
                'last_name' => 'Pascual',
                'ext_name' => '',
                'role' => 'student',
                'department_id' => 1,
                'stat' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        DB::table('users')->updateOrInsert(
            ['email' => 'KebinSy2121252@gmail.com'],
            [
                'student_id' => 'MAR' . str_pad(mt_rand(1, 999999999999), 12, '0', STR_PAD_LEFT),
                'password' => Hash::make('Password@123'),
                'first_name' => 'Andrei Kevin',
                'middle_name' => 'A.',
                'last_name' => 'Sy',
                'ext_name' => '',
                'role' => 'instructor',
                'department_id' => 1,
                'stat' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );


        
    }
}