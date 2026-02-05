<?php

namespace App\Http\Controllers;

use App\Models\InformationSheet;
use App\Models\TaskSheet;
use App\Models\TaskSheetItem;
use App\Http\Requests\StoreTaskSheetRequest;
use App\Http\Requests\UpdateTaskSheetRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TaskSheetController extends Controller
{
    public function create(InformationSheet $informationSheet)
    {
        return view('task-sheets.create', compact('informationSheet'));
    }

    public function store(StoreTaskSheetRequest $request, InformationSheet $informationSheet)
    {
        $validated = $request->validated();

        try {
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('task-sheets', 'public');
            }

            $taskSheet = TaskSheet::create([
                'information_sheet_id' => $informationSheet->id,
                'task_number' => $request->task_number,
                'title' => $request->title,
                'description' => $request->description,
                'instructions' => $request->instructions,
                'objectives' => $request->objectives,
                'materials' => $request->materials,
                'safety_precautions' => $request->safety_precautions ?? [],
                'image_path' => $imagePath,
            ]);

            foreach ($request->items as $itemData) {
                TaskSheetItem::create([
                    'task_sheet_id' => $taskSheet->id,
                    'part_name' => $itemData['part_name'],
                    'description' => $itemData['description'],
                    'expected_finding' => $itemData['expected_finding'],
                    'acceptable_range' => $itemData['acceptable_range'],
                    'order' => $itemData['order'] ?? 0,
                ]);
            }

            return redirect()->route('courses.index')
                ->with('success', 'Task sheet created successfully!');
        } catch (\Exception $e) {
            Log::error('Task sheet creation failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);
            return back()->withInput()->with('error', 'Failed to create task sheet. Please try again.');
        }
    }

    public function edit(InformationSheet $informationSheet, TaskSheet $taskSheet)
    {
        $taskSheet->load('items');
        return view('task-sheets.edit', compact('informationSheet', 'taskSheet'));
    }

    public function update(UpdateTaskSheetRequest $request, InformationSheet $informationSheet, TaskSheet $taskSheet)
    {
        $validated = $request->validated();

        try {
            $imagePath = $taskSheet->image_path;
            if ($request->hasFile('image')) {
                // Delete old image
                if ($imagePath) {
                    Storage::disk('public')->delete($imagePath);
                }
                $imagePath = $request->file('image')->store('task-sheets', 'public');
            }

            $taskSheet->update([
                'task_number' => $request->task_number,
                'title' => $request->title,
                'description' => $request->description,
                'instructions' => $request->instructions,
                'objectives' => $request->objectives,
                'materials' => $request->materials,
                'safety_precautions' => $request->safety_precautions ?? [],
                'image_path' => $imagePath,
            ]);

            return redirect()->route('courses.index')
                ->with('success', 'Task sheet updated successfully!');
        } catch (\Exception $e) {
            Log::error('Task sheet update failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);
            return back()->withInput()->with('error', 'Failed to update task sheet. Please try again.');
        }
    }

    public function destroy(InformationSheet $informationSheet, TaskSheet $taskSheet)
    {
        try {
            if ($taskSheet->image_path) {
                Storage::disk('public')->delete($taskSheet->image_path);
            }
            $taskSheet->items()->delete();
            $taskSheet->delete();

            return response()->json(['success' => 'Task sheet deleted successfully!']);
        } catch (\Exception $e) {
            Log::error('Task sheet deletion failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);
            return response()->json(['error' => 'Failed to delete task sheet. Please try again.'], 500);
        }
    }

    public function show(TaskSheet $taskSheet)
    {
        $taskSheet->load(['items', 'informationSheet.module.course']);
        return view('task-sheets.show', compact('taskSheet'));
    }

    public function submit(Request $request, TaskSheet $taskSheet)
    {
        $request->validate([
            'findings' => 'required|array',
            'findings.*' => 'required|string',
        ]);

        try {
            $submission = $taskSheet->submissions()->create([
                'user_id' => auth()->id(),
                'findings' => json_encode($request->findings),
                'submitted_at' => now(),
            ]);

            return redirect()->route('performance-criteria.create', ['taskSheet' => $taskSheet->id])
                ->with('success', 'Task sheet submitted successfully! Please complete the performance criteria.');
        } catch (\Exception $e) {
            Log::error('Task sheet submission failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);
            return back()->withInput()->with('error', 'Failed to submit task sheet. Please try again.');
        }
    }
}