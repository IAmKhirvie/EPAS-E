<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Module;
use App\Models\InformationSheet;
use App\Models\SelfCheck;
use App\Models\TaskSheet;
use App\Models\JobSheet;
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
                'course_id' => $course->id, // Add this line
                'sector' => 'Electronics',
                'qualification_title' => 'Electronic Products Assembly And Servicing NCII',
                'unit_of_competency' => 'Assemble Electronic Products',
                'module_title' => 'Assembling Electronic Products',
                'module_number' => 'Module 1',
                'module_name' => 'Competency Based Learning Material', 
                'how_to_use_cblm' => 'Welcome to the Module "Assembling Electronic Products". This module contains training materials and activities for you to complete.

    The unit of competency "Assemble Electronic Products" contains the knowledge, skills and attitudes required for Electronic Products Assembly and Servicing course Required to obtain the National Certificate(NC) level II.

    You are required to go through a series of learning activities in order to compete each of the learning outcomes of the module. In each learning outcome there are Information sheets, information sheets and activity sheets. Do this activity on your own and answer the Self Check at the end of each learning activity.

    If you have questions, do not hesitate to ask your teacher for assistance.

    Recognition of Prior Learning (RPL)
    
    You have already some basic knowledge and skills covered in this module. If you can demonstrate competence to your teacher in a particular skill, talk to him/her so you did not have to undergo the same training again. If you have a qualification or Certificate of Competency from previous trainings show it to him/her. If the skills you required are consistent with and relevant to this module, they become part of the evidence. You can present these RPL. If you are not sure about your competence skills, discuss this with your teacher.
    
    After completing this module, ask your teacher to assess your competence. Result of your assessment will be recorded in your competency profile. All the learning activities are designed for you to complete at your own pace.

    In this module, you will find the activities for you to accomplish and relevant Information sheets for each learning outcome. Each learning outcome may have more than one learning activity.
    
     This module is prepared to help you achieve the required competency in receiving and relaying information. This will be the source of information that will enable you to acquire the knowledge and skills in Electronic Products Assembly and Servicing NC II independently at your own pace with minimum supervision from your trainer

    ',
                'introduction' => 'This module contains information sheet(s) and suggested learning activities in Assembling Electronic Products. It includes instructions and procedure on how to Assemble Electronic Products.

    This module consists of five (5) learning outcomes. Learning outcomes contain learning activities supported by instruction sheets. Before you perform the instruction, read the information sheets and answer the self check and activities provided to ascertain to yourself and your teacher that you have acquired the knowledge necessary to perform the skill portion of the particular learning outcome.

    Upon completing this module, report to your teacher for assessment to check your achievement of knowledge and skills requirements of this module. If you pass the assessment, you will be given a certificate of completion.',
                'learning_outcomes' => 'Upon completion of the module the students shall be able to:
    1. Prepare to assemble electronics products
    2. Prepare/Make PCB modules
    3. Mount and solder electronic components
    4. Assemble electronic products
    5. Test and inspect assembled electronic products

    Note: additional instructions will be provided by the instructor.',
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

            // Add Self Check for Information Sheet 1.1
            SelfCheck::create([
                'information_sheet_id' => $infoSheet1->id,
                'check_number' => '1.1.1',
                'content' => '1. What is the difference between static electricity and current electricity?
    2. Name three sources of electricity.
    3. What are free electrons and why are they important in electronics?',
                'answer_key' => '1. Static electricity is stationary electric charge, while current electricity is the flow of electric charge.
    2. Batteries, generators, solar cells.
    3. Free electrons are electrons that are not bound to atoms and can move freely, enabling electrical conduction.',
                'order' => 1,
            ]);

            // Create Information Sheet 1.2
            $infoSheet2 = InformationSheet::create([
                'module_id' => $module->id,
                'sheet_number' => '1.2',
                'title' => 'Resistors, Color coding, Conversion, Tolerance, Circuits and Ohm\'s Law',
                'content' => 'This section covers resistors, their color coding, tolerance computation, testing using a multi-tester, Ohm\'s Law, electrical circuits, and different types of circuits.',
                'order' => 2,
            ]);

            // Add Self Checks for Information Sheet 1.2
            SelfCheck::create([
                'information_sheet_id' => $infoSheet2->id,
                'check_number' => '1.2.1',
                'content' => 'What are the color bands for a 1k ohm resistor with 5% tolerance?',
                'answer_key' => 'Brown, Black, Red, Gold',
                'order' => 1,
            ]);

            SelfCheck::create([
                'information_sheet_id' => $infoSheet2->id,
                'check_number' => '1.2.2',
                'content' => 'State Ohm\'s Law and write the formula.',
                'answer_key' => 'Ohm\'s Law states that the current through a conductor between two points is directly proportional to the voltage across the two points. Formula: V = I × R',
                'order' => 2,
            ]);

            // Add Task Sheet for Information Sheet 1.2
            TaskSheet::create([
                'information_sheet_id' => $infoSheet2->id,
                'sheet_number' => '1.2.1',
                'title' => 'Resistor Color Coding Practice',
                'objective' => 'To identify resistor values using color coding',
                'instructions' => '1. Gather 10 different resistors
    2. Identify the color bands on each resistor
    3. Calculate the resistance value and tolerance
    4. Verify using a multi-tester',
                'materials_needed' => 'Various resistors, multi-tester, notebook',
                'performance_criteria' => '• Correctly identify resistor values
    • Properly use multi-tester
    • Accurate calculations',
                'order' => 1,
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