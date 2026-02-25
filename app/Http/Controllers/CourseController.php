<?php

namespace App\Http\Controllers;

use App\Constants\Roles;
use App\Models\Course;
use App\Models\HomeworkSubmission;
use App\Models\Module;
use App\Models\SelfCheckSubmission;
use App\Models\User;
use App\Models\InformationSheet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $query = Course::where('is_active', true)
            ->withCount(['modules' => function($query) {
                $query->where('is_active', true);
            }])
            ->with('instructor');

        // Instructors only see their assigned courses
        if ($user->role === Roles::INSTRUCTOR) {
            $query->where('instructor_id', $user->id);
        }

        $courses = $query->orderBy('order')->get();

        return view('courses.index', compact('courses'));
    }

    public function contentManagement()
    {
        $user = Auth::user();

        $query = Course::with([
            'modules' => function($query) {
                $query->orderBy('order');
            },
            'modules.informationSheets' => function($query) {
                $query->orderBy('sheet_number')
                    ->with(['topics', 'selfChecks.questions', 'taskSheets', 'jobSheets', 'homeworks', 'checklists']);
            },
            'instructor'
        ]);

        // Instructors only see their assigned courses
        if ($user->role === Roles::INSTRUCTOR) {
            $query->where('instructor_id', $user->id);
        }

        $courses = $query->orderBy('order')->get();

        // Get instructors list for admin to assign
        $instructors = $user->role === Roles::ADMIN
            ? User::where('role', Roles::INSTRUCTOR)->where('stat', 1)->orderBy('last_name')->get()
            : collect();

        return view('content-management.index', compact('courses', 'instructors'));
    }

    public function create()
    {
        $this->authorize('create', Course::class);

        $instructors = User::where('role', Roles::INSTRUCTOR)->where('stat', 1)->orderBy('last_name')->get();
        return view('courses.create', compact('instructors'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Course::class);

        $validated = $request->validate([
            'course_name' => 'required|string|max:255',
            'course_code' => 'required|string|max:50|unique:courses',
            'description' => 'nullable|string',
            'sector' => 'nullable|string|max:255',
            'instructor_id' => 'nullable|exists:users,id',
        ]);

        try {
            $course = Course::create([
                'course_name' => $validated['course_name'],
                'course_code' => $validated['course_code'],
                'description' => $validated['description'],
                'sector' => $validated['sector'],
                'instructor_id' => $validated['instructor_id'] ?? null,
                'is_active' => true,
                'order' => Course::max('order') + 1,
            ]);

            return redirect()->route('courses.show', $course->id)
                ->with('success', 'Course created successfully!');

        } catch (\Exception $e) {
            Log::error('Course creation failed: ' . $e->getMessage());

            return back()->withInput()
                ->with('error', 'Failed to create course. Please try again.');
        }
    }

    public function show(Course $course)
    {
        $this->authorize('view', $course);
        $user = Auth::user();

        $course->load(['modules' => function($query) {
            $query->where('is_active', true)->orderBy('order');
        }, 'instructor']);

        $canEdit = $user->role === Roles::ADMIN || $course->instructor_id === $user->id;

        return view('courses.show', compact('course', 'canEdit'));
    }

    public function edit(Course $course)
    {
        $this->authorize('update', $course);
        $user = Auth::user();

        $instructors = $user->role === Roles::ADMIN
            ? User::where('role', Roles::INSTRUCTOR)->where('stat', 1)->orderBy('last_name')->get()
            : collect();

        return view('courses.edit', compact('course', 'instructors'));
    }

    public function update(Request $request, Course $course)
    {
        $this->authorize('update', $course);
        $user = Auth::user();

        $rules = [
            'course_name' => 'required|string|max:255',
            'course_code' => 'required|string|max:50|unique:courses,course_code,' . $course->id,
            'description' => 'nullable|string',
            'sector' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ];

        // Only admin can change instructor assignment
        if ($user->role === Roles::ADMIN) {
            $rules['instructor_id'] = 'nullable|exists:users,id';
        }

        $validated = $request->validate($rules);

        try {
            $course->update($validated);

            return redirect()->route('courses.show', $course->id)
                ->with('success', 'Course updated successfully!');

        } catch (\Exception $e) {
            Log::error('Course update failed: ' . $e->getMessage());

            return back()->withInput()
                ->with('error', 'Failed to update course. Please try again.');
        }
    }

    /**
     * Assign an instructor to a course (Admin only)
     */
    public function assignInstructor(Request $request, Course $course)
    {
        $this->authorizeAdmin('Only administrators can assign instructors.');

        $request->validate([
            'instructor_id' => 'nullable|exists:users,id',
        ]);

        try {
            $course->update(['instructor_id' => $request->instructor_id]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $request->instructor_id
                        ? 'Instructor assigned successfully.'
                        : 'Instructor removed from course.',
                ]);
            }

            return back()->with('success', 'Instructor updated successfully.');
        } catch (\Exception $e) {
            Log::error('Instructor assignment failed', [
                'error' => $e->getMessage(),
                'course_id' => $course->id,
                'user_id' => Auth::id(),
            ]);
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Failed to assign instructor. Please try again.'], 500);
            }
            return back()->with('error', 'Failed to assign instructor. Please try again.');
        }
    }

    public function destroy(Course $course)
    {
        try {
            DB::transaction(function () use ($course) {
                $course->delete(); // Cascades via model boot events
            });

            if (request()->expectsJson()) {
                return response()->json([
                    'message' => 'Course and all associated content deleted successfully!'
                ]);
            }

            return redirect()->route('content.management')
                ->with('success', 'Course and all associated content deleted successfully!');

        } catch (\Exception $e) {
            Log::error('Course deletion failed', [
                'course_id' => $course->id,
                'error' => $e->getMessage(),
            ]);

            if (request()->expectsJson()) {
                return response()->json([
                    'message' => 'Failed to delete course. Please try again.'
                ], 500);
            }

            return back()->with('error', 'Failed to delete course. Please try again.');
        }
    }
}