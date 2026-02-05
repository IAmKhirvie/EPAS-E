<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     *  @return void
     */
    public function run()
    {
        DB::table('departments')->updateOrInsert([
            [
                'name'=>'Instructor',
                'description'=>'Teaching and instructional department',
                'created_at'=>now(),
                'updated_at'=>now(),
            ],
            [
                'name'=>'Administration',
                'description'=>'Handles administrative tasks and operations',
                'created_at'=>now(),
                'updated_at'=>now(),
            ],
            [
                'name'=>'Registrar',
                'description'=>'Responsible for student records and registration',
                'created_at'=>now(),
                'updated_at'=>now(),
            ],
        ]);
    }
}