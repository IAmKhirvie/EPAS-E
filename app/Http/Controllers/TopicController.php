<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use App\Models\InformationSheet;
use App\Http\Requests\StoreTopicRequest;
use App\Http\Requests\UpdateTopicRequest;
use App\Services\ContentSanitizationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\AnnouncementController;


class TopicController extends Controller
{
    public function __construct(
        private ContentSanitizationService $sanitizer
    ) {}

    public function create($informationSheetId)
    {
        $informationSheet = InformationSheet::with(['module.course'])->findOrFail($informationSheetId);
        $nextOrder = $informationSheet->topics()->max('order') + 1;
        
        return view('modules.information-sheets.topics.create', compact('informationSheet', 'nextOrder'));
    }

    public function store(StoreTopicRequest $request, $informationSheetId)
    {
        Log::info('Topic store method called', [
            'information_sheet_id' => $informationSheetId,
            'user_id' => auth()->id(),
        ]);

        $informationSheet = InformationSheet::findOrFail($informationSheetId);

        $validated = $request->validated();

        try {
            Log::info('Validation passed', ['validated_data' => $validated]);

            // Use HTML Purifier for maximum security (without nl2br)
            if (!empty($validated['content'])) {
                $validated['content'] = $this->sanitizer->sanitizeWithHtmlPurifier($validated['content']);
            }

            // Process parts with images
            $parts = $this->sanitizer->processPartsWithImages($request, $validated['parts'] ?? []);
            $validated['parts'] = $parts;

            Log::info('Content sanitized successfully');

            $topic = $informationSheet->topics()->create($validated);
            
            // Load relationships for the announcement
            $informationSheet->load('module.course');
            $module = $informationSheet->module;
            $course = $module->course;
            
            $content = "New topic '{$topic->title}' (Topic {$topic->topic_number}) has been added to Information Sheet {$informationSheet->sheet_number} in Module {$module->module_number} of {$course->course_name}.";
            
            // Fix: Use the full class reference
            \App\Http\Controllers\AnnouncementController::createAutomaticAnnouncement(
                'topic', 
                $content, 
                auth()->user(), 
                'all' 
            );
            
            Log::info('Topic created and announcement sent');

            return redirect()->route('courses.index')
                ->with('success', "Topic '{$topic->title}' created successfully!");

        } catch (\Exception $e) {
            Log::error('Topic creation failed: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return back()->withInput()
                ->with('error', 'Failed to create topic. Please try again.');
        }
    }

    public function edit($informationSheetId, $topicId)
    {
        $informationSheet = InformationSheet::with(['module.course'])->findOrFail($informationSheetId);
        $topic = Topic::findOrFail($topicId);
        
        return view('modules.information-sheets.topics.edit', compact('informationSheet', 'topic'));
    }

    public function update(UpdateTopicRequest $request, $informationSheetId, $topicId)
    {
        $informationSheet = InformationSheet::findOrFail($informationSheetId);
        $topic = Topic::findOrFail($topicId);

        $validated = $request->validated();

        try {
            // Use HTML Purifier for maximum security (without nl2br)
            if (!empty($validated['content'])) {
                $validated['content'] = $this->sanitizer->sanitizeWithHtmlPurifier($validated['content']);
            }

            // Process parts with images (pass existing parts to handle image retention)
            $parts = $this->sanitizer->processPartsWithImages($request, $validated['parts'] ?? [], $topic->parts ?? []);
            $validated['parts'] = $parts;

            $topic->update($validated);

            return redirect()->route('courses.index')
                ->with('success', "Topic '{$topic->title}' updated successfully!");

        } catch (\Exception $e) {
            Log::error('Topic update failed: ' . $e->getMessage());

            return back()->withInput()
                ->with('error', 'Failed to update topic. Please try again.');
        }
    }

    public function destroy($topicId)
    {
        try {
            $topic = Topic::findOrFail($topicId);
            $topicTitle = $topic->title;
            $topic->delete();

            if (request()->expectsJson()) {
                return response()->json([
                    'message' => "Topic '{$topicTitle}' deleted successfully!"
                ]);
            }

            return redirect()->route('courses.index')
                ->with('success', "Topic '{$topicTitle}' deleted successfully!");

        } catch (\Exception $e) {
            Log::error('Topic deletion failed: ' . $e->getMessage());
            
            if (request()->expectsJson()) {
                return response()->json([
                    'message' => 'Failed to delete topic. Please try again.'
                ], 500);
            }
            
            return back()->with('error', 'Failed to delete topic. Please try again.');
        }
    }

    public function showContent($topicId)
    {
        $topic = Topic::with('informationSheet.module')->findOrFail($topicId);
        return view('modules.information-sheets.topics.content', compact('topic'));
    }

    public function getContent(Topic $topic)
    {
        try {
            // Return the topic content as HTML for AJAX requests
            $html = view('modules.information-sheets.topics.content-partial', compact('topic'))->render();

            return response()->json([
                'html' => $html
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading topic content: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to load topic content'
            ], 500);
        }
    }

    /**
     * Get topic content for AJAX loading in module show page
     */
    public function getTopicContent(InformationSheet $informationSheet, Topic $topic)
    {
        try {
            // Verify the topic belongs to this information sheet
            if ($topic->information_sheet_id !== $informationSheet->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Topic not found in this information sheet'
                ], 404);
            }

            $html = view('modules.information-sheets.topics.content-partial', compact('topic'))->render();

            return response()->json([
                'success' => true,
                'html' => $html,
                'topic' => [
                    'id' => $topic->id,
                    'title' => $topic->title,
                    'topic_number' => $topic->topic_number,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading topic content: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load topic content',
                'html' => '<div class="alert alert-danger">Failed to load content. Please refresh the page.</div>'
            ], 500);
        }
    }

    /**
     * Delete a specific part image
     */
    public function deletePartImage(Request $request, $topicId, $partIndex)
    {
        try {
            $topic = Topic::findOrFail($topicId);
            $parts = $topic->parts ?? [];

            if (isset($parts[$partIndex]) && !empty($parts[$partIndex]['image'])) {
                // Delete file from storage
                $filename = basename($parts[$partIndex]['image']);
                Storage::delete('public/topic-images/' . $filename);

                // Remove image from part
                $parts[$partIndex]['image'] = null;
                $topic->update(['parts' => $parts]);
            }

            return response()->json(['success' => true, 'message' => 'Image deleted successfully']);
        } catch (\Exception $e) {
            Log::error('Part image deletion failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to delete image'], 500);
        }
    }
}