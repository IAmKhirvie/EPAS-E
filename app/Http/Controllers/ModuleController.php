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
    public function index()
    {
        $modules = Module::with(['course', 'informationSheets'])
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        return view('modules.index', compact('modules'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Module::class);

        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id', // Add this
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
            $module = DB::transaction(function () use ($validated) {
                return Module::create([
                    'course_id' => $validated['course_id'],
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
                    'order' => Module::where('course_id', $validated['course_id'])->max('order') + 1,
                ]);
            });

            return redirect()->route('courses.show', $module->course_id)
                ->with('success', 'Module created successfully!');

        } catch (\Exception $e) {
            Log::error('Module creation failed: ' . $e->getMessage());
            
            return back()->withInput()
                ->with('error', 'Failed to create module. Please try again.');
        }
    }


    public function show(Module $module)
    {
        $module->load([
            'informationSheets.selfChecks',
            'informationSheets.topics'
        ]);

        return view('modules.show', compact('module'));
    }

    public function showInformationSheet(Module $module, InformationSheet $informationSheet)
    {
        // Get all sheets for navigation
        $allSheets = $module->informationSheets()->orderBy('sheet_number')->get();
        $currentIndex = $allSheets->search(function($sheet) use ($informationSheet) {
            return $sheet->id === $informationSheet->id;
        });
        
        $prevSheet = $currentIndex > 0 ? $allSheets[$currentIndex - 1] : null;
        $nextSheet = $currentIndex < $allSheets->count() - 1 ? $allSheets[$currentIndex + 1] : null;

        return view('modules.information-sheets.show', [
            'module' => $module,
            'informationSheet' => $informationSheet,
            'prevSheet' => $prevSheet,
            'nextSheet' => $nextSheet,
            'currentSheetNumber' => $currentIndex + 1,
            'totalSheets' => $allSheets->count()
        ]);
    }

    public function getContent(Module $module, $contentType)
    {
        try {
            // Return specific module content based on type
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

    public function showTopic(Module $module, InformationSheet $informationSheet, Topic $topic)
    {
        // Verify relationships
        if ($topic->information_sheet_id !== $informationSheet->id || 
            $informationSheet->module_id !== $module->id) {
            abort(404);
        }

        // Track progress via service
        $this->moduleService->trackTopicProgress($topic);

        return view('modules.topics.show', [
            'module' => $module,
            'informationSheet' => $informationSheet,
            'topic' => $topic,
            'nextTopic' => $topic->getNextTopic(),
            'prevTopic' => $topic->getPreviousTopic()
        ]);
    }

    public function submitSelfCheck(Request $request, SelfCheck $selfCheck)
    {
        $validated = $request->validate([
            'answers' => 'required|array',
            'time_spent' => 'required|integer'
        ]);

        $score = $this->moduleService->calculateScore($selfCheck, $validated['answers']);
        $maxScore = $this->moduleService->getMaxScore($selfCheck);
        $minScore = $selfCheck->min_score ?? ($maxScore * 0.7);
        $passed = $score >= $minScore;

        // Update user progress
        UserProgress::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'module_id' => $selfCheck->informationSheet->module_id,
                'progressable_type' => SelfCheck::class,
                'progressable_id' => $selfCheck->id
            ],
            [
                'status' => $passed ? 'passed' : 'failed',
                'score' => $score,
                'max_score' => $maxScore,
                'time_spent' => $validated['time_spent'],
                'answers' => $validated['answers'],
                'completed_at' => now()
            ]
        );

        return response()->json([
            'success' => true,
            'score' => $score,
            'max_score' => $maxScore,
            'min_score_required' => $minScore,
            'passed' => $passed
        ]);
    }
    
    public function create()
    {
        $user = Auth::user();

        // Filter courses based on user role
        $query = Course::where('is_active', true);
        if ($user->role === Roles::INSTRUCTOR) {
            $query->where('instructor_id', $user->id);
        }

        $courses = $query->orderBy('course_name')->get();
        return view('modules.create', compact('courses'));
    }

    public function getModuleProgress(Module $module)
    {
        $progress = $this->moduleService->getProgress($module, auth()->id());
        return response()->json($progress);
    }

    public function edit(Module $module)
    {
        $this->authorize('update', $module);
        $user = Auth::user();

        // Filter courses based on user role
        $query = Course::where('is_active', true);
        if ($user->role === Roles::INSTRUCTOR) {
            $query->where('instructor_id', $user->id);
        }

        $courses = $query->orderBy('course_name')->get();
        return view('modules.edit', compact('module', 'courses'));
    }

    public function update(Request $request, Module $module)
    {
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

    public function destroy(Module $module)
    {
        $this->authorize('delete', $module);

        try {
            // Check if module has information sheets
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

    /**
     * Upload image for module
     */
    public function uploadImage(Request $request, Module $module)
    {
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

    /**
     * Delete image from module
     */
    public function deleteImage(Request $request, Module $module, $imageIndex)
    {
        try {
            $this->moduleService->deleteImage($module, (int) $imageIndex);
            return redirect()->back()->with('success', 'Image deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Module image deletion failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete image. Please try again.');
        }
    }

    /**
     * Get sheet content via AJAX
     */
    public function getSheetContent(InformationSheet $informationSheet)
    {
        try {
            $informationSheet->load(['topics', 'selfChecks']);

            $html = view('modules.information-sheets.content-partial', [
                'sheet' => $informationSheet
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

    /**
     * Download module as printable HTML/PDF
     */
    public function downloadPdf(Module $module)
    {
        // Load all related content
        $module->load([
            'informationSheets.topics',
            'informationSheets.selfChecks.questions',
            'informationSheets.taskSheets',
            'informationSheets.jobSheets',
        ]);

        $exportDate = now()->format('F j, Y g:i A');

        // Render the PDF view
        $html = view('exports.module-pdf', compact('module', 'exportDate'))->render();

        // Return as downloadable HTML file (can be printed to PDF by user)
        $filename = \Illuminate\Support\Str::slug($module->module_name) . '-' . date('Y-m-d') . '.html';

        return Response::make($html, 200, [
            'Content-Type' => 'text/html',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Preview module for printing (opens in new tab)
     */
    public function printPreview(Module $module)
    {
        // Load all related content
        $module->load([
            'informationSheets.topics',
            'informationSheets.selfChecks.questions',
            'informationSheets.taskSheets',
            'informationSheets.jobSheets',
        ]);

        $exportDate = now()->format('F j, Y g:i A');

        return view('exports.module-pdf', compact('module', 'exportDate'));
    }
}