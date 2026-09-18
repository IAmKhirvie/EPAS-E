<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('courses') || !Schema::hasTable('modules')) {
            return;
        }

        // Only assign legacy modules that predate course ownership.
        if (!DB::table('modules')->whereNull('course_id')->exists()) {
            return;
        }

        $course = DB::table('courses')->where('course_code', 'IMPORTED-MODULES')->first();

        if (!$course) {
            $courseId = DB::table('courses')->insertGetId([
                'course_code' => 'IMPORTED-MODULES',
                'course_name' => 'Imported Modules',
                'description' => 'Modules imported before courses were introduced.',
                'sector' => 'General',
                'is_active' => true,
                'order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $courseId = $course->id;
        }

        // Update existing modules to belong to this course
        DB::table('modules')->whereNull('course_id')->update(['course_id' => $courseId]);
    }

    public function down()
    {
        // This migration cannot be reversed safely
    }
};
