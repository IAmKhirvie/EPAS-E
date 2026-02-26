<?php

namespace App\Http\Controllers;

use App\Constants\Roles;
use App\Models\Module;
use App\Models\InformationSheet;
use App\Models\Course;
use App\Models\Topic;
use App\Models\UserProgress;
use App\Models\SelfCheck;
use App\Services\ModuleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;


class ModuleController extends Controller
{
    protected ModuleService $moduleService;

    public function __construct(ModuleService $moduleService)
    {
        $this->moduleService = $moduleService;
    }

    public function store(Request $request, Course $course)
    {
        $this->authorize('create', Module::class);

        $validated = $request->validate([
            'qualification_title' => 'required|string|max:255',
            'unit_of_competency' => 'required|string|max:255',
            'module_title' => 'required|string|max:255',
            'module_number' => 'required|string|max:50',
            'module_name' => 'required|string|max:255',
            'table_of_contents' => 'nullable|string',
            'how_to_use_cblm' => 'nullable|string',
            'introduction' => 'nullable|string',
            'learning_outcomes' => 'nullable|string',
        ]);

        try {
            $module = DB::transaction(function () use ($validated, $course) {
                return Module::create([
                    'course_id' => $course->id,
                    'qualification_title' => $validated['qualification_title'],
                    'unit_of_competency' => $validated['unit_of_competency'],
                    'module_title' => $validated['module_title'],
                    'module_number' => $validated['module_number'],
                    'module_name' => $validated['module_name'],
                    'table_of_contents' => $validated['table_of_contents'],
                    'how_to_use_cblm' => $validated['how_to_use_cblm'],
                    'introduction' => $validated['introduction'],
                    'learning_outcomes' => $validated['learning_outcomes'],
                    'is_active' => true,
                    'order' => Module::where('course_id', $course->id)->max('order') + 1,
                ]);
            });

            return redirect()->route('courses.show', $course)
                ->with('success', 'Module created successfully!');

        } catch (\Exception $e) {
            Log::error('Module creation failed: ' . $e->getMessage());

            return back()->withInput()
                ->with('error', 'Failed to create module. Please try again.');
        }
    }

    public function show(Course $course, Module $module, ?string $slug = null)
    {
        $this->verifyModuleBelongsToCourse($course, $module);

        // Redirect to canonical URL with slug if missing
        if ($slug === null && $module->slug) {
            return redirect()->route('courses.modules.show', [$course, $module, $module->slug]);
        }

        $module->load([
            'informationSheets.selfChecks',
            'informationSheets.taskSheets',
            'informationSheets.jobSheets',
            'informationSheets.topics',
            'course',
        ]);

        return view('modules.show-unified', compact('module', 'course'));
    }

    public function showInformationSheet(Course $course, Module $module, InformationSheet $informationSheet)
    {
        $this->verifyModuleBelongsToCourse($course, $module);

        // Get all sheets for navigation
        $allSheets = $module->informationSheets()->orderBy('sheet_number')->get();
        $currentIndex = $allSheets->search(function($sheet) use ($informationSheet) {
            return $sheet->id === $informationSheet->id;
        });

        $prevSheet = $currentIndex > 0 ? $allSheets[$currentIndex - 1] : null;
        $nextSheet = $currentIndex < $allSheets->count() - 1 ? $allSheets[$currentIndex + 1] : null;

        return view('modules.information-sheets.show', [
            'module' => $module,
            'course' => $course,
            'informationSheet' => $informationSheet,
            'prevSheet' => $prevSheet,
            'nextSheet' => $nextSheet,
            'currentSheetNumber' => $currentIndex + 1,
            'totalSheets' => $allSheets->count()
        ]);
    }

    public function getContent(Course $course, Module $module, $contentType)
    {
        $this->verifyModuleBelongsToCourse($course, $module);

        try {
            $viewMap = [
                'introduction' => 'modules.content.introduction',
                'electric-history' => 'modules.content.electric-history',
                'static-electricity' => 'modules.content.static-electricity',
                'free-electrons' => 'modules.content.free-electrons',
                'alternative-energy' => 'modules.content.alternative-energy',
                'electric-energy' => 'modules.content.electric-energy',
                'materials' => 'modules.content.materials',
                'self-check' => 'modules.content.self-check'
            ];

            if (!array_key_exists($contentType, $viewMap)) {
                return response()->json(['error' => 'Content type not found'], 404);
            }

            return response()->json([
                'html' => view($viewMap[$contentType], compact('module'))->render()
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading module content: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to load module content'
            ], 500);
        }
    }

    public function showTopic(Course $course, Module $module, InformationSheet $informationSheet, Topic $topic)
    {
        $this->verifyModuleBelongsToCourse($course, $module);

        if ($topic->information_sheet_id !== $informationSheet->id ||
            $informationSheet->module_id !== $module->id) {
            abort(404);
        }

        $this->moduleService->trackTopicProgress($topic);

        return view('modules.topics.show', [
            'module' => $module,
            'course' => $course,
            'informationSheet' => $informationSheet,
            'topic' => $topic,
            'nextTopic' => $topic->getNextTopic(),
            'prevTopic' => $topic->getPreviousTopic()
        ]);
    }

    public function getTopicContent(Course $course, Module $module, InformationSheet $informationSheet, Topic $topic)
    {
        $this->verifyModuleBelongsToCourse($course, $module);

        if ($topic->information_sheet_id !== $informationSheet->id ||
            $informationSheet->module_id !== $module->id) {
            return response()->json(['success' => false, 'message' => 'Not found'], 404);
        }

        try {
            $html = view('modules.information-sheets.topics.content-partial', [
                'topic' => $topic,
            ])->render();

            return response()->json(['success' => true, 'html' => $html]);
        } catch (\Exception $e) {
            Log::error('Failed to load topic content: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to load topic.']);
        }
    }

    public function create(Course $course)
    {
        $user = Auth::user();

        // Verify instructor owns this course
        if ($user->role === Roles::INSTRUCTOR && $course->instructor_id !== $user->id) {
            abort(403);
        }

        $courses = Course::where('is_active', true)
            ->when($user->role === Roles::INSTRUCTOR, fn ($q) => $q->where('instructor_id', $user->id))
            ->orderBy('course_name')
            ->get();

        return view('modules.create', compact('courses', 'course'));
    }

    public function getModuleProgress(Course $course, Module $module)
    {
        $this->verifyModuleBelongsToCourse($course, $module);

        $progress = $this->moduleService->getProgress($module, auth()->id());
        return response()->json($progress);
    }

    public function edit(Course $course, Module $module)
    {
        $this->verifyModuleBelongsToCourse($course, $module);
        $this->authorize('update', $module);
        $user = Auth::user();

        $courses = Course::where('is_active', true)
            ->when($user->role === Roles::INSTRUCTOR, fn ($q) => $q->where('instructor_id', $user->id))
            ->orderBy('course_name')
            ->get();

        return view('modules.edit', compact('module', 'courses', 'course'));
    }

    public function update(Request $request, Course $course, Module $module)
    {
        $this->verifyModuleBelongsToCourse($course, $module);
        $this->authorize('update', $module);

        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'qualification_title' => 'required|string|max:255',
            'unit_of_competency' => 'required|string|max:255',
            'module_title' => 'required|string|max:255',
            'module_number' => 'required|string|max:50',
            'module_name' => 'required|string|max:255',
            'table_of_contents' => 'nullable|string',
            'how_to_use_cblm' => 'nullable|string',
            'introduction' => 'nullable|string',
            'learning_outcomes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        try {
            $module->update($validated);

            return redirect()->route('courses.show', $module->course_id)
                ->with('success', 'Module updated successfully!');

        } catch (\Exception $e) {
            Log::error('Module update failed: ' . $e->getMessage());

            return back()->withInput()
                ->with('error', 'Failed to update module. Please try again.');
        }
    }

    public function destroy(Course $course, Module $module)
    {
        $this->verifyModuleBelongsToCourse($course, $module);
        $this->authorize('delete', $module);

        try {
            if ($module->informationSheets()->count() > 0) {
                if (request()->expectsJson()) {
                    return response()->json([
                        'message' => 'Cannot delete module that has information sheets. Please delete the information sheets first.'
                    ], 422);
                }
                return back()->with('error', 'Cannot delete module that has information sheets. Please delete the information sheets first.');
            }

            $courseId = $module->course_id;
            $module->delete();

            if (request()->expectsJson()) {
                return response()->json([
                    'message' => 'Module deleted successfully!'
                ]);
            }

            return redirect()->route('courses.show', $courseId)
                ->with('success', 'Module deleted successfully!');

        } catch (\Exception $e) {
            Log::error('Module deletion failed: ' . $e->getMessage());

            if (request()->expectsJson()) {
                return response()->json([
                    'message' => 'Failed to delete module. Please try again.'
                ], 500);
            }

            return back()->with('error', 'Failed to delete module. Please try again.');
        }
    }

    public function uploadImage(Request $request, Course $course, Module $module)
    {
        $this->verifyModuleBelongsToCourse($course, $module);

        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|mimetypes:image/jpeg,image/png,image/gif,image/webp|max:' . config('joms.uploads.max_image_size', 5120),
            'caption' => 'nullable|string|max:255',
            'section' => 'nullable|string|max:100',
        ]);

        try {
            $this->moduleService->uploadImage($module, $request->image, $request->caption, $request->section);
            return redirect()->back()->with('success', 'Image uploaded successfully!');
        } catch (\Exception $e) {
            Log::error('Module image upload failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to upload image. Please try again.');
        }
    }

    public function deleteImage(Request $request, Course $course, Module $module, $imageIndex)
    {
        $this->verifyModuleBelongsToCourse($course, $module);

        try {
            $this->moduleService->deleteImage($module, (int) $imageIndex);
            return redirect()->back()->with('success', 'Image deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Module image deletion failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete image. Please try again.');
        }
    }

    public function getSheetContent(Course $course, Module $module, InformationSheet $informationSheet)
    {
        $this->verifyModuleBelongsToCourse($course, $module);

        try {
            $informationSheet->load(['topics', 'selfChecks', 'taskSheets', 'jobSheets']);

            $html = view('modules.information-sheets.content-partial', [
                'sheet' => $informationSheet,
                'course' => $course,
                'module' => $module,
            ])->render();

            return response()->json([
                'success' => true,
                'html' => $html,
                'sheet' => [
                    'id' => $informationSheet->id,
                    'title' => $informationSheet->title,
                    'sheet_number' => $informationSheet->sheet_number,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to load sheet content: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to load content. Please try again.',
                'html' => '<div class="alert alert-danger">Failed to load content. Please refresh the page.</div>'
            ]);
        }
    }

    public function getSelfCheckContent(Course $course, Module $module, InformationSheet $informationSheet)
    {
        $this->verifyModuleBelongsToCourse($course, $module);

        try {
            $informationSheet->load(['selfChecks.questions']);
            $selfCheck = $informationSheet->selfChecks->first();

            if (!$selfCheck) {
                return response()->json([
                    'success' => false,
                    'html' => '<div class="alert alert-info">No self-check available for this information sheet.</div>'
                ]);
            }

            $html = view('modules.partials.self-check-inline', [
                'selfCheck' => $selfCheck,
                'informationSheet' => $informationSheet,
                'module' => $module,
                'course' => $course,
            ])->render();

            return response()->json(['success' => true, 'html' => $html]);
        } catch (\Exception $e) {
            Log::error('Failed to load self-check content: ' . $e->getMessage());
            return response()->json(['success' => false, 'html' => '<div class="alert alert-danger">Failed to load self-check.</div>']);
        }
    }

    public function getTaskSheetContent(Course $course, Module $module, InformationSheet $informationSheet)
    {
        $this->verifyModuleBelongsToCourse($course, $module);

        try {
            $informationSheet->load(['taskSheets.items']);
            $taskSheet = $informationSheet->taskSheets->first();

            if (!$taskSheet) {
                return response()->json([
                    'success' => false,
                    'html' => '<div class="alert alert-info">No task sheet available for this information sheet.</div>'
                ]);
            }

            $html = view('modules.partials.task-sheet-inline', [
                'taskSheet' => $taskSheet,
                'informationSheet' => $informationSheet,
                'module' => $module,
                'course' => $course,
            ])->render();

            return response()->json(['success' => true, 'html' => $html]);
        } catch (\Exception $e) {
            Log::error('Failed to load task sheet content: ' . $e->getMessage());
            return response()->json(['success' => false, 'html' => '<div class="alert alert-danger">Failed to load task sheet.</div>']);
        }
    }

    public function getJobSheetContent(Course $course, Module $module, InformationSheet $informationSheet)
    {
        $this->verifyModuleBelongsToCourse($course, $module);

        try {
            $informationSheet->load(['jobSheets.steps']);
            $jobSheet = $informationSheet->jobSheets->first();

            if (!$jobSheet) {
                return response()->json([
                    'success' => false,
                    'html' => '<div class="alert alert-info">No job sheet available for this information sheet.</div>'
                ]);
            }

            $html = view('modules.partials.job-sheet-inline', [
                'jobSheet' => $jobSheet,
                'informationSheet' => $informationSheet,
                'module' => $module,
                'course' => $course,
            ])->render();

            return response()->json(['success' => true, 'html' => $html]);
        } catch (\Exception $e) {
            Log::error('Failed to load job sheet content: ' . $e->getMessage());
            return response()->json(['success' => false, 'html' => '<div class="alert alert-danger">Failed to load job sheet.</div>']);
        }
    }

    public function downloadPdf(Course $course, Module $module)
    {
        $this->verifyModuleBelongsToCourse($course, $module);

        $module->load([
            'informationSheets.topics',
            'informationSheets.selfChecks.questions',
            'informationSheets.taskSheets',
            'informationSheets.jobSheets',
        ]);

        $exportDate = now()->format('F j, Y g:i A');

        $html = view('exports.module-pdf', compact('module', 'exportDate'))->render();

        $filename = \Illuminate\Support\Str::slug($module->module_name) . '-' . date('Y-m-d') . '.html';

        return Response::make($html, 200, [
            'Content-Type' => 'text/html',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function printPreview(Course $course, Module $module)
    {
        $this->verifyModuleBelongsToCourse($course, $module);

        $module->load([
            'informationSheets.topics',
            'informationSheets.selfChecks.questions',
            'informationSheets.taskSheets',
            'informationSheets.jobSheets',
        ]);

        $exportDate = now()->format('F j, Y g:i A');

        return view('exports.module-pdf', compact('module', 'exportDate'));
    }

    private function verifyModuleBelongsToCourse(Course $course, Module $module): void
    {
        if ($module->course_id !== $course->id) {
            abort(404);
        }
    }
}
