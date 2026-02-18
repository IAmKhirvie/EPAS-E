<?php

namespace App\Http\Controllers;

use App\Models\InformationSheet;
use App\Models\JobSheet;
use App\Models\JobSheetStep;
use App\Http\Requests\StoreJobSheetRequest;
use App\Http\Requests\UpdateJobSheetRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class JobSheetController extends Controller
{
    public function create(InformationSheet $informationSheet)
    {
        return view('job-sheets.create', compact('informationSheet'));
    }

    public function store(StoreJobSheetRequest $request, InformationSheet $informationSheet)
    {
        $validated = $request->validated();

        try {
            DB::transaction(function () use ($request, $informationSheet) {
                $jobSheet = JobSheet::create([
                    'information_sheet_id' => $informationSheet->id,
                    'job_number' => $request->job_number,
                    'title' => $request->title,
                    'description' => $request->description,
                    'objectives' => $request->objectives,
                    'tools_required' => $request->tools_required,
                    'safety_requirements' => $request->safety_requirements,
                    'reference_materials' => $request->reference_materials ?? [],
                ]);

                foreach ($request->steps as $stepData) {
                    $imagePath = null;
                    if (isset($stepData['image']) && $stepData['image']->isValid()) {
                        $imagePath = $stepData['image']->store('job-sheet-steps', 'public');
                    }

                    JobSheetStep::create([
                        'job_sheet_id' => $jobSheet->id,
                        'step_number' => $stepData['step_number'],
                        'instruction' => $stepData['instruction'],
                        'expected_outcome' => $stepData['expected_outcome'],
                        'image_path' => $imagePath,
                    ]);
                }
            });

            return redirect()->route('courses.index')
                ->with('success', 'Job sheet created successfully!');
        } catch (\Exception $e) {
            Log::error('Job sheet creation failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);
            return back()->withInput()->with('error', 'Failed to create job sheet. Please try again.');
        }
    }

    public function edit(InformationSheet $informationSheet, JobSheet $jobSheet)
    {
        $jobSheet->load('steps');
        return view('job-sheets.edit', compact('informationSheet', 'jobSheet'));
    }

    public function update(UpdateJobSheetRequest $request, InformationSheet $informationSheet, JobSheet $jobSheet)
    {
        $validated = $request->validated();

        try {
            $jobSheet->update([
                'job_number' => $request->job_number,
                'title' => $request->title,
                'description' => $request->description,
                'objectives' => $request->objectives,
                'tools_required' => $request->tools_required,
                'safety_requirements' => $request->safety_requirements,
                'reference_materials' => $request->reference_materials ?? [],
            ]);

            return redirect()->route('courses.index')
                ->with('success', 'Job sheet updated successfully!');
        } catch (\Exception $e) {
            Log::error('Job sheet update failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);
            return back()->withInput()->with('error', 'Failed to update job sheet. Please try again.');
        }
    }

    public function destroy(InformationSheet $informationSheet, JobSheet $jobSheet)
    {
        try {
            // Delete step images
            foreach ($jobSheet->steps as $step) {
                if ($step->image_path) {
                    Storage::disk('public')->delete($step->image_path);
                }
            }

            $jobSheet->steps()->delete();
            $jobSheet->delete();

            return response()->json(['success' => 'Job sheet deleted successfully!']);
        } catch (\Exception $e) {
            Log::error('Job sheet deletion failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);
            return response()->json(['error' => 'Failed to delete job sheet. Please try again.'], 500);
        }
    }

    public function show(JobSheet $jobSheet)
    {
        $jobSheet->load(['steps', 'informationSheet.module.course']);
        return view('job-sheets.show', compact('jobSheet'));
    }

    public function submit(Request $request, JobSheet $jobSheet)
    {
        $request->validate([
            'completed_steps' => 'required|array',
            'observations' => 'required|string',
            'challenges' => 'nullable|string',
            'solutions' => 'nullable|string',
        ]);

        try {
            $submission = $jobSheet->submissions()->create([
                'user_id' => auth()->id(),
                'completed_steps' => json_encode($request->completed_steps),
                'observations' => $request->observations,
                'challenges' => $request->challenges,
                'solutions' => $request->solutions,
                'submitted_at' => now(),
            ]);

            return redirect()->route('performance-criteria.create', ['jobSheet' => $jobSheet->id])
                ->with('success', 'Job sheet submitted successfully! Please complete the performance criteria.');
        } catch (\Exception $e) {
            Log::error('Job sheet submission failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);
            return back()->withInput()->with('error', 'Failed to submit job sheet. Please try again.');
        }
    }
}