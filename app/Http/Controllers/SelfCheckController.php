<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\InformationSheet;
use App\Models\SelfCheck;
use App\Models\SelfCheckQuestion;
use App\Services\SelfCheckGradingService;
use App\Http\Requests\StoreSelfCheckRequest;
use App\Http\Requests\UpdateSelfCheckRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * SelfCheckController
 *
 * Handles self-check/quiz creation, editing, and submissions.
 *
 * Supported Question Types:
 * - multiple_choice: Select one correct answer from options
 * - multiple_select: Select multiple correct answers (checkboxes)
 * - true_false: True or False statement
 * - fill_blank: Fill in missing words (supports multiple blanks)
 * - short_answer: Free-text response with keyword matching
 * - numeric: Number answer with tolerance range
 * - matching: Connect Column A items to Column B items
 * - ordering: Arrange items in correct sequence
 * - classification: Sort items into categories
 * - image_choice: Select correct answer from image options
 * - image_identification: "Name this picture" - type what you see
 * - hotspot: Click on specific area of image
 * - image_labeling: Label parts of a diagram
 * - audio_question: Listen to audio and answer
 * - video_question: Watch video and answer
 * - drag_drop: Drag items to target zones
 * - slider: Numeric slider for range answers
 */
class SelfCheckController extends Controller
{
    public function __construct(private SelfCheckGradingService $gradingService)
    {
    }

    /*
    |--------------------------------------------------------------------------
    | Create & Store
    |--------------------------------------------------------------------------
    */

    /**
     * Show the self-check creation form.
     */
    public function create(InformationSheet $informationSheet)
    {
        return view('modules.self-checks.create', compact('informationSheet'));
    }

    /**
     * Store a new self-check with questions.
     */
    public function store(StoreSelfCheckRequest $request, InformationSheet $informationSheet)
    {
        $validated = $request->validated();

        try {
            DB::transaction(function () use ($request, $informationSheet) {
                // Calculate total points
                $totalPoints = 0;
                foreach ($request->questions as $q) {
                    $totalPoints += (int) ($q['points'] ?? 1);
                }

                $selfCheck = SelfCheck::create([
                    'information_sheet_id' => $informationSheet->id,
                    'check_number' => $request->check_number,
                    'title' => $request->title,
                    'description' => $request->description,
                    'instructions' => $request->instructions,
                    'time_limit' => $request->time_limit,
                    'passing_score' => $request->passing_score ?? config('joms.grading.default_passing_score', 70),
                    'total_points' => $totalPoints,
                ]);

                $order = 0;
                foreach ($request->questions as $questionData) {
                    $order++;

                    // Process options based on question type
                    $options = $this->processQuestionOptions(
                        $questionData['question_type'],
                        $questionData['options'] ?? []
                    );

                    // Process correct answer
                    $correctAnswer = $this->processCorrectAnswer(
                        $questionData['question_type'],
                        $questionData['correct_answer'] ?? null,
                        $options
                    );

                    SelfCheckQuestion::create([
                        'self_check_id' => $selfCheck->id,
                        'question_text' => $questionData['question_text'],
                        'question_type' => $questionData['question_type'],
                        'points' => $questionData['points'],
                        'options' => $options,
                        'correct_answer' => $correctAnswer,
                        'explanation' => $questionData['explanation'] ?? null,
                        'order' => $order,
                    ]);
                }
            });

            if ($request->input('redirect') === 'continue') {
                return redirect()->route('self-checks.create', $informationSheet)
                    ->with('success', 'Self-check created! You can add another.');
            }

            return redirect()->route('courses.index')
                ->with('success', 'Self-check created successfully!');
        } catch (\Exception $e) {
            Log::error('Self-check store failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);
            return back()->withInput()->with('error', 'Failed to create self-check. Please try again.');
        }
    }

    /**
     * Process question options based on type.
     */
    private function processQuestionOptions(string $type, array $options): ?array
    {
        if (empty($options) && !in_array($type, ['true_false'])) {
            return null;
        }

        switch ($type) {
            case 'multiple_choice':
                // Filter out empty options
                return array_values(array_filter($options, fn($opt) => is_string($opt) && !empty(trim($opt))));

            case 'multiple_select':
                // Same as multiple_choice but allows multiple correct answers
                return array_values(array_filter($options, fn($opt) => is_string($opt) && !empty(trim($opt))));

            case 'numeric':
                // Store tolerance, unit, and decimal places
                return [
                    'tolerance' => floatval($options['tolerance'] ?? 0),
                    'unit' => $options['unit'] ?? null,
                    'decimal_places' => intval($options['decimal_places'] ?? 2),
                ];

            case 'classification':
                // Store categories, items, and their mappings
                $categories = array_values(array_filter(
                    $options['categories'] ?? [],
                    fn($c) => is_string($c) && !empty(trim($c))
                ));
                $items = array_values(array_filter(
                    $options['items'] ?? [],
                    fn($i) => is_string($i) && !empty(trim($i))
                ));
                return [
                    'categories' => $categories,
                    'items' => $items,
                    'item_categories' => $options['item_categories'] ?? [],
                ];

            case 'image_identification':
                // Store main image and acceptable answers
                return [
                    'main_image' => $options['main_image'] ?? null,
                    'acceptable_answers' => array_map('trim', explode(',', $options['acceptable_answers'] ?? '')),
                ];

            case 'hotspot':
                // Store hotspot image and target coordinates
                return [
                    'hotspot_image' => $options['hotspot_image'] ?? null,
                    'hotspot_x' => floatval($options['hotspot_x'] ?? 50),
                    'hotspot_y' => floatval($options['hotspot_y'] ?? 50),
                    'hotspot_radius' => floatval($options['hotspot_radius'] ?? 10),
                ];

            case 'image_labeling':
                // Store label image and label positions
                $labels = array_values(array_filter(
                    $options['labels'] ?? [],
                    fn($l) => is_string($l) && !empty(trim($l))
                ));
                return [
                    'label_image' => $options['label_image'] ?? null,
                    'labels' => $labels,
                    'label_positions' => $options['label_positions'] ?? [],
                ];

            case 'audio_question':
                // Store audio URL and response settings
                return [
                    'audio_url' => $options['audio_url'] ?? null,
                    'play_limit' => intval($options['play_limit'] ?? 0),
                    'response_type' => $options['response_type'] ?? 'text',
                    'mc_options' => isset($options['mc_options'])
                        ? array_values(array_filter($options['mc_options'], fn($o) => !empty(trim($o ?? ''))))
                        : null,
                ];

            case 'video_question':
                // Store video URL and response settings
                return [
                    'video_url' => $options['video_url'] ?? null,
                    'start_time' => intval($options['start_time'] ?? 0),
                    'end_time' => !empty($options['end_time']) ? intval($options['end_time']) : null,
                    'response_type' => $options['response_type'] ?? 'text',
                    'mc_options' => isset($options['mc_options'])
                        ? array_values(array_filter($options['mc_options'], fn($o) => !empty(trim($o ?? ''))))
                        : null,
                ];

            case 'drag_drop':
                // Store draggables, dropzones, and correct mapping
                $draggables = array_values(array_filter(
                    $options['draggables'] ?? [],
                    fn($d) => is_string($d) && !empty(trim($d))
                ));
                $dropzones = array_values(array_filter(
                    $options['dropzones'] ?? [],
                    fn($d) => is_string($d) && !empty(trim($d))
                ));
                return [
                    'draggables' => $draggables,
                    'dropzones' => $dropzones,
                    'correct_mapping' => $options['correct_mapping'] ?? [],
                ];

            case 'slider':
                // Store slider range and settings
                return [
                    'min' => floatval($options['min'] ?? 0),
                    'max' => floatval($options['max'] ?? 100),
                    'step' => floatval($options['step'] ?? 1),
                    'tolerance' => floatval($options['tolerance'] ?? 0),
                    'unit' => $options['unit'] ?? null,
                ];

            case 'matching':
                // Build pairs from left/right arrays
                $pairs = [];
                $left = $options['left'] ?? [];
                $right = $options['right'] ?? [];

                for ($i = 0; $i < max(count($left), count($right)); $i++) {
                    if (!empty(trim($left[$i] ?? '')) && !empty(trim($right[$i] ?? ''))) {
                        $pairs[] = [
                            'left' => trim($left[$i]),
                            'right' => trim($right[$i]),
                        ];
                    }
                }
                return ['pairs' => $pairs];

            case 'ordering':
                // Filter out empty items
                $items = array_filter($options, fn($opt) => is_string($opt) && !empty(trim($opt)));
                return ['items' => array_values($items)];

            case 'image_choice':
                // Build options with labels and images
                $imageOptions = [];
                $labels = $options['labels'] ?? [];
                $images = $options['images'] ?? [];

                for ($i = 0; $i < count($labels); $i++) {
                    if (!empty(trim($labels[$i] ?? ''))) {
                        $imageOptions[] = [
                            'label' => trim($labels[$i]),
                            'image' => trim($images[$i] ?? ''),
                        ];
                    }
                }
                return $imageOptions;

            case 'short_answer':
                // Store model answer if provided
                if (isset($options['model_answer'])) {
                    return ['model_answer' => $options['model_answer']];
                }
                return null;

            case 'true_false':
                return null;

            default:
                return $options;
        }
    }

    /**
     * Process correct answer based on question type.
     */
    private function processCorrectAnswer(string $type, $answer, ?array $options): ?string
    {
        if ($answer === null && !in_array($type, ['matching', 'ordering', 'classification', 'drag_drop', 'hotspot', 'image_labeling'])) {
            return null;
        }

        switch ($type) {
            case 'multiple_choice':
            case 'image_choice':
                // Store the index as correct answer
                return (string) $answer;

            case 'multiple_select':
                // Store array of correct indices as JSON
                if (is_array($answer)) {
                    return json_encode(array_map('intval', $answer));
                }
                return $answer;

            case 'true_false':
                return $answer === 'true' || $answer === true ? 'true' : 'false';

            case 'fill_blank':
                // Store comma-separated acceptable answers
                return $answer;

            case 'numeric':
            case 'slider':
                // Store the numeric value
                return (string) floatval($answer);

            case 'matching':
                // For matching, correct answer is the pairs mapping (stored in options)
                return 'matching';

            case 'ordering':
                // For ordering, correct answer is the original order (stored in options)
                return 'ordering';

            case 'classification':
                // Correct mapping stored in options
                return 'classification';

            case 'image_identification':
                // Store comma-separated acceptable answers
                return $answer;

            case 'hotspot':
                // Correct coordinates stored in options
                return 'hotspot';

            case 'image_labeling':
                // Correct labels stored in options
                return 'labeling';

            case 'audio_question':
            case 'video_question':
                // Depends on response_type (text or multiple_choice)
                return $answer;

            case 'drag_drop':
                // Correct mapping stored in options
                return 'drag_drop';

            case 'short_answer':
                // Keywords for auto-grading
                return $answer;

            default:
                return $answer;
        }
    }

    /**
     * Upload an image for a question.
     */
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|mimetypes:image/jpeg,image/png,image/gif,image/webp|max:' . config('joms.uploads.max_image_size', 5120),
        ]);

        try {
            $file = $request->file('image');
            $filename = 'quiz_' . uniqid() . '.' . $file->getClientOriginalExtension();

            // Store in public storage
            $path = $file->storeAs('quiz-images', $filename, 'public');

            return response()->json([
                'success' => true,
                'url' => Storage::url($path),
                'path' => $path,
            ]);
        } catch (\Exception $e) {
            Log::error('Self-check image upload failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);
            return response()->json(['error' => 'Failed to upload image. Please try again.'], 500);
        }
    }

    public function edit(InformationSheet $informationSheet, SelfCheck $selfCheck)
    {
        $selfCheck->load('questions');
        return view('modules.self-checks.edit', compact('informationSheet', 'selfCheck'));
    }

    public function update(UpdateSelfCheckRequest $request, InformationSheet $informationSheet, SelfCheck $selfCheck)
    {
        $validated = $request->validated();

        try {
            $selfCheck->update([
                'check_number' => $request->check_number,
                'title' => $request->title,
                'description' => $request->description,
                'instructions' => $request->instructions,
                'time_limit' => $request->time_limit,
                'passing_score' => $request->passing_score,
            ]);

            return redirect()->route('courses.index')
                ->with('success', 'Self-check updated successfully!');
        } catch (\Exception $e) {
            Log::error('Self-check update failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);
            return back()->withInput()->with('error', 'Failed to update self-check. Please try again.');
        }
    }

    public function destroy(InformationSheet $informationSheet, SelfCheck $selfCheck)
    {
        try {
            $selfCheck->questions()->delete();
            $selfCheck->delete();

            return response()->json(['success' => 'Self-check deleted successfully!']);
        } catch (\Exception $e) {
            Log::error('Self-check destroy failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);
            return response()->json(['error' => 'Failed to delete self-check. Please try again.'], 500);
        }
    }

    public function show(SelfCheck $selfCheck)
    {
        $selfCheck->load(['questions', 'informationSheet.module.course']);
        return view('modules.self-checks.show', compact('selfCheck'));
    }

    /**
     * Show self-check by module and information sheet
     */
    public function showBySheet(Module $module, InformationSheet $informationSheet)
    {
        // Verify the information sheet belongs to this module
        if ($informationSheet->module_id !== $module->id) {
            abort(404);
        }

        // Get the self-check for this information sheet
        $selfCheck = SelfCheck::where('information_sheet_id', $informationSheet->id)->first();

        if (!$selfCheck) {
            return redirect()->route('modules.show', $module->id)
                ->with('info', 'No self-check available for this information sheet yet.');
        }

        $selfCheck->load(['questions', 'informationSheet.module.course']);
        return view('modules.self-checks.show', compact('selfCheck'));
    }

    /*
    |--------------------------------------------------------------------------
    | Quiz Taking & Submission
    |--------------------------------------------------------------------------
    */

    /**
     * Submit quiz answers and calculate score.
     */
    public function submit(Request $request, SelfCheck $selfCheck)
    {
        $request->validate([
            'answers' => 'required|array',
        ]);

        try {
            $score = 0;
            $totalPoints = $selfCheck->total_points;
            $results = [];

            foreach ($selfCheck->questions as $question) {
                $userAnswer = $request->answers[$question->id] ?? null;
                $isCorrect = $this->gradingService->gradeQuestion($question, $userAnswer);
                $pointsEarned = 0;

                if ($isCorrect === true) {
                    $pointsEarned = $question->points;
                    $score += $pointsEarned;
                } elseif (is_numeric($isCorrect)) {
                    // Partial credit (for matching/ordering)
                    $pointsEarned = round($question->points * $isCorrect, 2);
                    $score += $pointsEarned;
                }

                $results[] = [
                    'question' => $question,
                    'user_answer' => $userAnswer,
                    'is_correct' => $isCorrect === true,
                    'partial_credit' => is_numeric($isCorrect) ? $isCorrect : null,
                    'points_earned' => $pointsEarned,
                ];
            }

            $percentage = $this->gradingService->calculatePercentage($score, $totalPoints);
            $passed = $this->gradingService->isPassing($percentage, $selfCheck->passing_score);

            // Save submission to database
            $submission = $selfCheck->submissions()->create([
                'user_id' => auth()->id(),
                'score' => $score,
                'total_points' => $totalPoints,
                'percentage' => $percentage,
                'passed' => $passed,
                'answers' => json_encode($request->answers),
                'completed_at' => now(),
            ]);

            return view('modules.self-checks.results', compact(
                'selfCheck', 'submission', 'results', 'score', 'totalPoints', 'percentage', 'passed'
            ));
        } catch (\Exception $e) {
            Log::error('Self-check submit failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);
            return back()->withInput()->with('error', 'Failed to submit self-check. Please try again.');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Media Upload Endpoints
    |--------------------------------------------------------------------------
    */

    /**
     * Upload an audio file for audio questions.
     */
    public function uploadAudio(Request $request)
    {
        $request->validate([
            'audio' => 'required|mimes:mp3,wav,ogg,m4a,webm|mimetypes:audio/mpeg,audio/wav,audio/ogg,audio/mp4,audio/x-m4a,audio/webm|max:' . config('joms.uploads.max_audio_size', 20480),
        ]);

        try {
            $file = $request->file('audio');
            $filename = 'quiz_audio_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('quiz-audio', $filename, 'public');

            return response()->json([
                'success' => true,
                'url' => Storage::url($path),
                'path' => $path,
            ]);
        } catch (\Exception $e) {
            Log::error('Self-check audio upload failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);
            return response()->json(['error' => 'Failed to upload audio. Please try again.'], 500);
        }
    }

    /**
     * Upload a video file for video questions.
     */
    public function uploadVideo(Request $request)
    {
        $request->validate([
            'video' => 'required|mimes:mp4,webm,ogg,mov|mimetypes:video/mp4,video/webm,video/ogg,video/quicktime|max:' . config('joms.uploads.max_video_size', 102400),
        ]);

        try {
            $file = $request->file('video');
            $filename = 'quiz_video_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('quiz-video', $filename, 'public');

            return response()->json([
                'success' => true,
                'url' => Storage::url($path),
                'path' => $path,
            ]);
        } catch (\Exception $e) {
            Log::error('Self-check video upload failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);
            return response()->json(['error' => 'Failed to upload video. Please try again.'], 500);
        }
    }
}
