<?php

namespace Database\Seeders;

use App\Models\InformationSheet;
use App\Models\SelfCheck;
use App\Models\SelfCheckQuestion;
use App\Models\TaskSheet;
use App\Models\JobSheet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActivitySeeder extends Seeder
{
    public function run()
    {
        DB::beginTransaction();

        try {
            // ===== SHEET 1.1 — Self Check 1.1.1 =====
            $sheet11 = InformationSheet::where('sheet_number', '1.1')->first();
            if ($sheet11) {
                $sc = SelfCheck::updateOrCreate(
                    ['information_sheet_id' => $sheet11->id, 'check_number' => '1.1.1'],
                    [
                        'title' => 'Introduction to Electronics and Electricity',
                        'description' => 'Fill in the blanks to test your understanding of basic electronics concepts.',
                        'instructions' => 'Fill in the blanks by choosing the correct answer. Pre-test will show choices, Post-test will not.',
                        'passing_score' => 70,
                        'total_points' => 30,
                        'is_active' => true,
                    ]
                );
                $sc->questions()->delete();

                $questions = [
                    ['text' => 'It is the flow of electrons along a conductor', 'type' => 'identification', 'answer' => 'Electricity', 'points' => 1],
                    ['text' => 'It is the application of electrical principle', 'type' => 'identification', 'answer' => 'Electronics', 'points' => 1],
                    ['text' => 'They discovered Electricity', 'type' => 'identification', 'answer' => 'Greek Philosophers', 'points' => 1],
                    ['text' => 'It is the material they used to discover the Electricity', 'type' => 'identification', 'answer' => 'Amber', 'points' => 1],
                    ['text' => 'It is a device that converts mechanical energy into electrical energy', 'type' => 'identification', 'answer' => 'Generator', 'points' => 1],
                    ['text' => 'It is a part of an Atom that moves, and produce current', 'type' => 'identification', 'answer' => 'Electron', 'points' => 1],
                    ['text' => 'It is considered as best conductor', 'type' => 'identification', 'answer' => 'Gold', 'points' => 1],
                    ['text' => 'Copper has an atomic no. of how many?', 'type' => 'identification', 'answer' => '29', 'points' => 1],
                    ['text' => 'It is the source of Electricity that uses sunlight from Photovoltaic of the sun energy to electrical energy', 'type' => 'identification', 'answer' => 'Solar Power', 'points' => 1],
                    ['text' => 'It is the source of Electricity that uses Water\'s flow to provide mechanical energy to electrical energy', 'type' => 'identification', 'answer' => 'Hydropower', 'points' => 1],
                    ['text' => 'It is a material which Electricity can pass easily', 'type' => 'identification', 'answer' => 'Conductor', 'points' => 1],
                    ['text' => 'It is a process of splitting atoms into smaller atoms that release heat and radiation', 'type' => 'identification', 'answer' => 'Fission', 'points' => 1],
                    ['text' => 'If the Electrons on the last orbit/shell is not stable, it is also called ___', 'type' => 'identification', 'answer' => 'Free Electron', 'points' => 1],
                    ['text' => 'It is an electronic device that converts AC to DC', 'type' => 'identification', 'answer' => 'Rectifier', 'points' => 1],
                    ['text' => 'Give the 3 parts of an Atom', 'type' => 'enumeration', 'answer' => 'Proton, Neutron, Electron', 'points' => 3],
                    ['text' => 'Give the 3 types of Materials', 'type' => 'enumeration', 'answer' => 'Conductor, Insulator, Semiconductor', 'points' => 3],
                    ['text' => 'Give the 2 categories of current (complete)', 'type' => 'enumeration', 'answer' => 'Direct Current (DC), Alternating Current (AC)', 'points' => 2],
                    ['text' => 'Give at least 4 Sources of Electricity', 'type' => 'enumeration', 'answer' => 'Solar, Hydropower, Nuclear, Geothermal, Wind, Biomass', 'points' => 4],
                ];

                foreach ($questions as $i => $q) {
                    SelfCheckQuestion::create([
                        'self_check_id' => $sc->id,
                        'question_text' => $q['text'],
                        'question_type' => $q['type'],
                        'correct_answer' => $q['answer'],
                        'points' => $q['points'],
                        'order' => $i + 1,
                    ]);
                }
            }

            // ===== SHEET 1.2 — Self Check 1.2.1 (Multiple Choice) =====
            $sheet12 = InformationSheet::where('sheet_number', '1.2')->first();
            if ($sheet12) {
                $sc = SelfCheck::updateOrCreate(
                    ['information_sheet_id' => $sheet12->id, 'check_number' => '1.2.1'],
                    [
                        'title' => 'Resistors, Color Coding and Ohm\'s Law',
                        'description' => 'Multiple choice quiz on resistors, color coding, and Ohm\'s Law.',
                        'instructions' => 'Choose the letter of the correct answer.',
                        'passing_score' => 70,
                        'total_points' => 5,
                        'is_active' => true,
                    ]
                );
                $sc->questions()->delete();

                $mcQuestions = [
                    ['text' => 'It is an electronic device that resists, limits or opposes the amount of current in a circuit:', 'answer' => 'Resistor', 'options' => ['Resistor', 'Capacitor', 'Diode', 'None of the above']],
                    ['text' => 'A kind of resistor that can vary the resistance:', 'answer' => 'Variable resistor', 'options' => ['Fixed resistor', 'Potentiometer', 'Volume control', 'Variable resistor']],
                    ['text' => 'A circuit with only one possible path that the electrical current may flow:', 'answer' => 'Series', 'options' => ['Parallel', 'Series', 'Series-Parallel', 'None of the above']],
                    ['text' => 'The computation of accepted value of resistor\'s resistance from minimum to maximum:', 'answer' => 'Tolerance', 'options' => ['Decoding', 'Conversion', 'Tolerance', 'None of the above']],
                    ['text' => 'It states that an electrical circuit\'s current is directly proportional to its voltage, and inversely proportional to its resistance:', 'answer' => 'Ohm\'s Law', 'options' => ['Fuse law', 'Ohm\'s Law', 'Volt\'s law', 'None of the above']],
                ];

                foreach ($mcQuestions as $i => $q) {
                    SelfCheckQuestion::create([
                        'self_check_id' => $sc->id,
                        'question_text' => $q['text'],
                        'question_type' => 'multiple_choice',
                        'correct_answer' => $q['answer'],
                        'options' => $q['options'],
                        'points' => 1,
                        'order' => $i + 1,
                    ]);
                }

                // Task Sheet 1.2.1
                TaskSheet::updateOrCreate(
                    ['information_sheet_id' => $sheet12->id, 'task_number' => '1.2.1'],
                    [
                        'title' => 'Checking and Testing of Resistor',
                        'description' => 'To understand the value of color coded resistor, to maximize the use of Multi-meter and to identify the conditions of a resistor.',
                        'instructions' => "1. Decode the color thru value\n2. Compute the minimum and maximum tolerance\n3. Using the suggested ohm's range, check using the multi-meter\n4. Write down the parts, description (R1,R2,etc.), actual reading (resistance), the range use and the remarks if good or defective\n5. Submit to Trainer for checking",
                        'estimated_duration' => 30,
                        'difficulty_level' => 'beginner',
                    ]
                );
            }

            // ===== SHEET 1.3 — Self Check 1.3.1 + Task Sheets =====
            $sheet13 = InformationSheet::where('sheet_number', '1.3')->first();
            if ($sheet13) {
                $sc = SelfCheck::updateOrCreate(
                    ['information_sheet_id' => $sheet13->id, 'check_number' => '1.3.1'],
                    [
                        'title' => 'Capacitors and Diodes',
                        'description' => 'Enumeration quiz on capacitors and diodes.',
                        'instructions' => 'Answer the following enumeration questions.',
                        'passing_score' => 70,
                        'total_points' => 10,
                        'is_active' => true,
                    ]
                );
                $sc->questions()->delete();

                SelfCheckQuestion::create(['self_check_id' => $sc->id, 'question_text' => 'Give at least 4 kinds of diodes and illustrate the schematic symbol.', 'question_type' => 'enumeration', 'correct_answer' => 'Rectifier diode, Zener diode, LED, Schottky diode', 'points' => 4, 'order' => 1]);
                SelfCheckQuestion::create(['self_check_id' => $sc->id, 'question_text' => 'Give at least 4 kinds of capacitors.', 'question_type' => 'enumeration', 'correct_answer' => 'Ceramic, Electrolytic, Film, Tantalum', 'points' => 4, 'order' => 2]);
                SelfCheckQuestion::create(['self_check_id' => $sc->id, 'question_text' => 'Give the 2 parts of a Diode.', 'question_type' => 'enumeration', 'correct_answer' => 'Anode, Cathode', 'points' => 2, 'order' => 3]);

                // Task Sheet 1.3.1
                TaskSheet::updateOrCreate(
                    ['information_sheet_id' => $sheet13->id, 'task_number' => '1.3.1'],
                    [
                        'title' => 'Checking and Testing of Capacitor',
                        'description' => 'To understand the value of capacitor, to intensify the use of Multi-meter and to identify the conditions of a capacitor.',
                        'instructions' => "1. Check the value of capacitor\n2. Check the polarity of the terminal\n3. Using the suggested range in testing a capacitor\n4. Use the tester in Reverse bias\n5. Write down the parts, description, findings, range and remarks\n6. Submit to Trainer for checking",
                        'estimated_duration' => 30,
                        'difficulty_level' => 'beginner',
                    ]
                );

                // Task Sheet 1.3.2
                TaskSheet::updateOrCreate(
                    ['information_sheet_id' => $sheet13->id, 'task_number' => '1.3.2'],
                    [
                        'title' => 'Checking and Testing of Diode',
                        'description' => 'To understand the value of diode, to intensify the use of Multi-meter and to identify the conditions of a diode.',
                        'instructions' => "1. Check the type of diode\n2. Check the polarity of the terminal\n3. Using the multi-meter, check by performing both reverse and forward bias\n4. Write down the parts, description, findings, range and remarks\n5. Submit to Trainer for checking",
                        'estimated_duration' => 30,
                        'difficulty_level' => 'beginner',
                    ]
                );
            }

            // ===== SHEET 1.4 — Self Check 1.4.1 + Task Sheet =====
            $sheet14 = InformationSheet::where('sheet_number', '1.4')->first();
            if ($sheet14) {
                $sc = SelfCheck::updateOrCreate(
                    ['information_sheet_id' => $sheet14->id, 'check_number' => '1.4.1'],
                    [
                        'title' => 'Transistors, ICs and Transformers',
                        'description' => 'Multiple choice and enumeration quiz on transistors, integrated circuits, and transformers.',
                        'instructions' => 'Choose the letter of the correct answer for 1-5. Answer enumeration for 6-15.',
                        'passing_score' => 70,
                        'total_points' => 15,
                        'is_active' => true,
                    ]
                );
                $sc->questions()->delete();

                $q14 = [
                    ['text' => 'It is a semiconductor device that can be used for amplification, oscillation, rectification and switching', 'type' => 'multiple_choice', 'answer' => 'Transistor', 'options' => ['Transistor', 'Capacitor', 'Diode', 'None of the above'], 'points' => 1],
                    ['text' => 'A type of IC with a rectangular housing and two parallel rows of electrical connecting pins', 'type' => 'multiple_choice', 'answer' => 'Dual In-line Package (DIP) IC', 'options' => ['Linear IC', 'Dual In-line Package (DIP) IC', 'Surface Mounted Device (SMD) IC', 'None of the above'], 'points' => 1],
                    ['text' => 'An electro-magnetic device that is commonly used for step-down of voltage', 'type' => 'multiple_choice', 'answer' => 'Transformer', 'options' => ['Power supply', 'Integrated circuit', 'Transformer', 'None of the above'], 'points' => 1],
                    ['text' => 'All types of modern electronic devices. They are integrated, meaning they are made as a total circuit and housed in one enclosure', 'type' => 'multiple_choice', 'answer' => 'Integrated circuit', 'options' => ['Power supply', 'Integrated circuit', 'Transistor', 'None of the above'], 'points' => 1],
                    ['text' => 'It is a process of a transistor, that allows to move back and forth', 'type' => 'multiple_choice', 'answer' => 'Oscillation', 'options' => ['Amplification', 'Rectification', 'Oscillation', 'None of the above'], 'points' => 1],
                    ['text' => 'Give at least two (2) types of integrated circuit', 'type' => 'enumeration', 'answer' => 'DIP IC, SMD IC', 'options' => null, 'points' => 2],
                    ['text' => 'What are the three (3) parts of a Transistor?', 'type' => 'enumeration', 'answer' => 'Base, Emitter, Collector', 'options' => null, 'points' => 3],
                    ['text' => 'What are the three (3) parts of a Transformer?', 'type' => 'enumeration', 'answer' => 'Primary winding, Secondary winding, Core', 'options' => null, 'points' => 3],
                    ['text' => 'Give at least two (2) types of rectification process', 'type' => 'enumeration', 'answer' => 'Half-wave rectification, Full-wave rectification', 'options' => null, 'points' => 2],
                ];

                foreach ($q14 as $i => $q) {
                    SelfCheckQuestion::create([
                        'self_check_id' => $sc->id,
                        'question_text' => $q['text'],
                        'question_type' => $q['type'],
                        'correct_answer' => $q['answer'],
                        'options' => $q['options'],
                        'points' => $q['points'],
                        'order' => $i + 1,
                    ]);
                }

                // Task Sheet 1.4.1
                TaskSheet::updateOrCreate(
                    ['information_sheet_id' => $sheet14->id, 'task_number' => '1.4.1'],
                    [
                        'title' => 'Checking and Testing of Transistor',
                        'description' => 'To understand the value of transistor, to intensify the use of Multi-meter and to identify the conditions of a transistor.',
                        'instructions' => "1. Check the value of Transistor\n2. Using the suggested range in testing a terminal of Transistor in finding the base, emitter and collector\n3. Use the tester in both reverse and forward bias\n4. Write down the parts, description (Q1,Q2,etc.), findings, range and remarks\n5. Submit to Trainer for checking",
                        'estimated_duration' => 30,
                        'difficulty_level' => 'intermediate',
                    ]
                );
            }

            // ===== SHEET 1.5 — Self Check 1.5.1 + Task Sheets =====
            $sheet15 = InformationSheet::where('sheet_number', '1.5')->first();
            if ($sheet15) {
                $sc = SelfCheck::updateOrCreate(
                    ['information_sheet_id' => $sheet15->id, 'check_number' => '1.5.1'],
                    [
                        'title' => 'Schematic Diagram Interpretation',
                        'description' => 'Interpret schematic diagrams by drawing and connecting pictorial diagrams.',
                        'instructions' => 'Interpret the given Schematic Diagram by drawing and connecting its Pictorial Diagram.',
                        'passing_score' => 70,
                        'total_points' => 10,
                        'is_active' => true,
                    ]
                );
                $sc->questions()->delete();
                SelfCheckQuestion::create(['self_check_id' => $sc->id, 'question_text' => 'Interpret the given Schematic Diagram by drawing and connecting its Pictorial Diagram. (Refer to provided schematic diagrams)', 'question_type' => 'essay', 'correct_answer' => 'Student should correctly trace and draw the pictorial diagram matching the schematic.', 'points' => 10, 'order' => 1]);

                // Task Sheet 1.5.1
                TaskSheet::updateOrCreate(
                    ['information_sheet_id' => $sheet15->id, 'task_number' => '1.5.1'],
                    [
                        'title' => 'Drawing and Tracing of Inventory - Linear Power Supply',
                        'description' => 'To draw and trace electronic components and connection; to intensify the use of inventory / linear power supply; and to identify the result of it.',
                        'instructions' => "1. Check all parts and components\n2. Draw the schematic and write all values\n3. Write the description, designation, qty, actual reading, range and remarks\n4. Add the total value of each parts\n5. Submit to Trainer for checking",
                        'estimated_duration' => 45,
                        'difficulty_level' => 'intermediate',
                    ]
                );

                // Task Sheet 1.5.2
                TaskSheet::updateOrCreate(
                    ['information_sheet_id' => $sheet15->id, 'task_number' => '1.5.2'],
                    [
                        'title' => 'Drawing and Tracing of Inventory - Flip-Flop',
                        'description' => 'Checking and tracing of Inventory by using the multi-meter and tracing its connection for accuracy.',
                        'instructions' => "1. Check all parts and components\n2. Draw the schematic and write all the values of each components\n3. Add the total value of the parts, describe each component, write the designation, qty, actual reading, range and remarks\n4. Submit to Trainer for checking",
                        'estimated_duration' => 45,
                        'difficulty_level' => 'intermediate',
                    ]
                );
            }

            // ===== SHEET 1.6 — Job Sheets =====
            $sheet16 = InformationSheet::where('sheet_number', '1.6')->first();
            if ($sheet16) {
                // Job Sheet 1.6.1
                JobSheet::updateOrCreate(
                    ['information_sheet_id' => $sheet16->id, 'job_number' => '1.6.1'],
                    [
                        'title' => 'Soldering - Full-Wave Bridge Type Multi Tap Transformer',
                        'description' => 'To apply correct and proper technique on how soldering; know and understand soldering procedures used for connecting electronic components and repair.',
                        'procedures' => "Supplies and Materials needed: Soldering iron, lead, flux, PCB, and passive components, Multi-tester, Magnifier, Sand Paper.\n\nRecommended Method: Demonstration and Observation. Ask the trainer for safety usage and make/do the proper exercise of soldering.\n\nAssemble the Full-Wave Bridge Type Multi Tap Transformer circuit following the schematic diagram (220v input, selector switch, bridge rectifier D1-D4, and output).",
                        'estimated_duration' => 60,
                        'difficulty_level' => 'advanced',
                    ]
                );

                // Job Sheet 1.6.2
                JobSheet::updateOrCreate(
                    ['information_sheet_id' => $sheet16->id, 'job_number' => '1.6.2'],
                    [
                        'title' => 'Soldering - Flip-Flop Circuit Assembly',
                        'description' => 'To apply correct and proper technique of soldering; should know and understand soldering procedures used for connecting electronic components and assembly.',
                        'procedures' => "Supplies and Materials needed: Soldering iron, lead, flux, and passive components. C, R, LED (red + green) x4 pcs each, Transistor x2. Perfboard with copper clad: 1x3.\n\nTools and Equipment: Multi-tester, Cutter, Long Nose, Magnifier.\n\nRecommended Method: Demonstration of assembly/soldering. Hands-on exercise (Plug and Play). Follow the Flip-Flop schematic diagram and test.\n\nComponents: 470R (x2), 10k (x2), 100u (x2), LED1 Red, LED2 Green, Q1 BC 547, Q2 BC 547, Sw, 9v.",
                        'estimated_duration' => 90,
                        'difficulty_level' => 'advanced',
                    ]
                );
            }

            DB::commit();
            $this->command->info('Self-checks, Task Sheets, and Job Sheets seeded successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Activity seeder failed: ' . $e->getMessage());
            throw $e;
        }
    }
}
