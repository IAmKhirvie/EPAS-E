<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Module;
use App\Models\InformationSheet;
use App\Models\SelfCheck;
use App\Models\SelfCheckQuestion;
use App\Models\TaskSheet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModuleSeeder extends Seeder
{
    public function run()
    {
        // Start a database transaction
        DB::beginTransaction();

        try {
            // First, create or get the EPAS Course
            $course = Course::firstOrCreate(
                ['course_code' => 'EPAS-NCII'],
                [
                    'course_name' => 'Electronic Products Assembly and Servicing NCII',
                    'description' => 'This course covers the competencies required to assemble and service electronic products according to industry standards.',
                    'sector' => 'Electronics',
                    'is_active' => true,
                    'order' => 1
                ]
            );

            // Create the module under this course
            $module = Module::create([
                'course_id' => $course->id,
                'sector' => 'Electronics',
                'qualification_title' => 'Electronic Products Assembly And Servicing NCII',
                'unit_of_competency' => 'Assemble Electronic Products',
                'module_title' => 'Assembling Electronic Products',
                'module_number' => 'Module 1',
                'module_name' => 'Competency Based Learning Material',
                'how_to_use_cblm' => 'Welcome to the Module "Assembling Electronic Products". This module contains training materials and activities for you to complete.

The unit of competency "Assemble Electronic Products" contains the knowledge, skills and attitudes required for Electronic Products Assembly and Servicing course Required to obtain the National Certificate(NC) level II.

You are required to go through a series of learning activities in order to compete each of the learning outcomes of the module. In each learning outcome there are Information sheets, information sheets and activity sheets. Do this activity on your own and answer the Self Check at the end of each learning activity.

If you have questions, do not hesitate to ask your teacher for assistance.',
                'introduction' => 'This module contains information sheet(s) and suggested learning activities in Assembling Electronic Products. It includes instructions and procedure on how to Assemble Electronic Products.

This module consists of five (5) learning outcomes. Learning outcomes contain learning activities supported by instruction sheets.',
                'learning_outcomes' => 'Upon completion of the module the students shall be able to:
1. Prepare to assemble electronics products
2. Prepare/Make PCB modules
3. Mount and solder electronic components
4. Assemble electronic products
5. Test and inspect assembled electronic products',
                'is_active' => true,
                'order' => 1,
            ]);

            // Create Information Sheet 1.1
            $infoSheet1 = InformationSheet::create([
                'module_id' => $module->id,
                'sheet_number' => '1.1',
                'title' => 'Introduction to Electronics and Electricity',
                'content' => 'This information sheet covers the fundamental concepts of electronics and electricity, including electric history, static electricity, free electrons, sources of electricity, alternative energy, types of electric energy and current, and types of materials used in electronics.',
                'order' => 1,
            ]);

            // Add Self Check for Information Sheet 1.1 (new normalized schema)
            $selfCheck1 = SelfCheck::create([
                'information_sheet_id' => $infoSheet1->id,
                'check_number' => '1.1.1',
                'title' => 'Electronics Fundamentals Quiz',
                'description' => 'Test your understanding of basic electronics and electricity concepts.',
                'instructions' => 'Answer the following questions based on Information Sheet 1.1. Read each question carefully before answering.',
                'time_limit' => 15,
                'passing_score' => 70,
                'total_points' => 15,
                'is_active' => true,
            ]);

            // Add questions for the self check
            SelfCheckQuestion::create([
                'self_check_id' => $selfCheck1->id,
                'question_text' => 'What is the difference between static electricity and current electricity?',
                'question_type' => 'essay',
                'points' => 5,
                'correct_answer' => 'Static electricity is stationary electric charge, while current electricity is the flow of electric charge.',
                'explanation' => 'Static electricity involves charges that are not moving, while current electricity involves the continuous flow of electrons through a conductor.',
                'order' => 1,
            ]);

            SelfCheckQuestion::create([
                'self_check_id' => $selfCheck1->id,
                'question_text' => 'Name three sources of electricity.',
                'question_type' => 'enumeration',
                'points' => 5,
                'correct_answer' => 'Batteries, generators, solar cells',
                'explanation' => 'Common sources include chemical (batteries), mechanical (generators), and photovoltaic (solar cells).',
                'order' => 2,
            ]);

            SelfCheckQuestion::create([
                'self_check_id' => $selfCheck1->id,
                'question_text' => 'Free electrons are electrons bound tightly to atoms.',
                'question_type' => 'true_false',
                'points' => 5,
                'correct_answer' => 'false',
                'explanation' => 'Free electrons are NOT bound to atoms - they can move freely, which enables electrical conduction.',
                'order' => 3,
            ]);

            // Create Information Sheet 1.2
            $infoSheet2 = InformationSheet::create([
                'module_id' => $module->id,
                'sheet_number' => '1.2',
                'title' => 'Resistors, Color coding, Conversion, Tolerance, Circuits and Ohm\'s Law',
                'content' => 'This section covers resistors, their color coding, tolerance computation, testing using a multi-tester, Ohm\'s Law, electrical circuits, and different types of circuits.',
                'order' => 2,
            ]);

            // Add Self Check for Information Sheet 1.2
            $selfCheck2 = SelfCheck::create([
                'information_sheet_id' => $infoSheet2->id,
                'check_number' => '1.2.1',
                'title' => 'Resistor Color Coding Quiz',
                'description' => 'Test your knowledge of resistor color codes and Ohm\'s Law.',
                'instructions' => 'Answer the questions about resistor identification and Ohm\'s Law calculations.',
                'time_limit' => 10,
                'passing_score' => 70,
                'total_points' => 10,
                'is_active' => true,
            ]);

            SelfCheckQuestion::create([
                'self_check_id' => $selfCheck2->id,
                'question_text' => 'What are the color bands for a 1k ohm resistor with 5% tolerance?',
                'question_type' => 'identification',
                'points' => 5,
                'correct_answer' => 'Brown, Black, Red, Gold',
                'explanation' => 'Brown=1, Black=0, Red=x100, Gold=5% tolerance. 10 x 100 = 1000 ohms = 1k ohm.',
                'order' => 1,
            ]);

            SelfCheckQuestion::create([
                'self_check_id' => $selfCheck2->id,
                'question_text' => 'What formula represents Ohm\'s Law?',
                'question_type' => 'multiple_choice',
                'points' => 5,
                'correct_answer' => 'V = I × R',
                'explanation' => 'Ohm\'s Law states voltage equals current times resistance.',
                'order' => 2,
            ]);

            // Add Task Sheet for Information Sheet 1.2 (new normalized schema)
            $taskSheet = TaskSheet::create([
                'information_sheet_id' => $infoSheet2->id,
                'task_number' => '1.2.1',
                'title' => 'Resistor Color Coding Practice',
                'description' => 'Practice identifying resistor values using color coding.',
                'instructions' => '1. Gather 10 different resistors
2. Identify the color bands on each resistor
3. Calculate the resistance value and tolerance
4. Verify using a multi-tester
5. Record your findings in a table',
                'estimated_duration' => 30,
                'difficulty_level' => 'beginner',
            ]);

            // Add objectives to task sheet
            DB::table('task_sheet_objectives')->insert([
                ['task_sheet_id' => $taskSheet->id, 'objective' => 'Identify resistor values using color coding', 'order' => 1, 'created_at' => now(), 'updated_at' => now()],
                ['task_sheet_id' => $taskSheet->id, 'objective' => 'Properly use a multi-tester to verify resistance', 'order' => 2, 'created_at' => now(), 'updated_at' => now()],
                ['task_sheet_id' => $taskSheet->id, 'objective' => 'Calculate tolerance range for resistors', 'order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ]);

            // Add materials to task sheet
            DB::table('task_sheet_materials')->insert([
                ['task_sheet_id' => $taskSheet->id, 'material_name' => 'Various resistors (10 pcs)', 'quantity' => '10', 'order' => 1, 'created_at' => now(), 'updated_at' => now()],
                ['task_sheet_id' => $taskSheet->id, 'material_name' => 'Multi-tester', 'quantity' => '1', 'order' => 2, 'created_at' => now(), 'updated_at' => now()],
                ['task_sheet_id' => $taskSheet->id, 'material_name' => 'Notebook', 'quantity' => '1', 'order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ]);

            DB::commit();

            $this->command->info('EPAS Course and Module seeder completed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Module seeder failed: ' . $e->getMessage());
            throw $e;
        }
    }
}
