<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use App\Models\InformationSheet;
use App\Http\Requests\StoreTopicRequest;
use App\Http\Requests\UpdateTopicRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\AnnouncementController;


class TopicController extends Controller
{
    public function create($informationSheetId)
    {
        $informationSheet = InformationSheet::with(['module.course'])->findOrFail($informationSheetId);
        $nextOrder = $informationSheet->topics()->max('order') + 1;
        
        return view('modules.information-sheets.topics.create', compact('informationSheet', 'nextOrder'));
    }

    public function store(StoreTopicRequest $request, $informationSheetId)
    {
        // Debug: Log the request
        Log::info('Topic store method called', [
            'information_sheet_id' => $informationSheetId,
            'request_data' => $request->all()
        ]);

        $informationSheet = InformationSheet::findOrFail($informationSheetId);

        $validated = $request->validated();

        try {
            Log::info('Validation passed', ['validated_data' => $validated]);

            // Use HTML Purifier for maximum security (without nl2br)
            if (!empty($validated['content'])) {
                $validated['content'] = $this->sanitizeWithHtmlPurifier($validated['content']);
            }

            // Process parts with images
            $parts = $this->processPartsWithImages($request, $validated['parts'] ?? []);
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
                $validated['content'] = $this->sanitizeWithHtmlPurifier($validated['content']);
            }

            // Process parts with images (pass existing parts to handle image retention)
            $parts = $this->processPartsWithImages($request, $validated['parts'] ?? [], $topic->parts ?? []);
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
     * Most Secure: Using HTML Purifier
     * Protects against all known XSS attacks while allowing basic formatting
     */
    private function sanitizeWithHtmlPurifier($content)
    {
        // Check if HTML Purifier is available
        if (!class_exists('HTMLPurifier')) {
            Log::warning('HTMLPurifier not found, using fallback sanitization');
            // Fallback to basic security if HTML Purifier isn't installed
            return $this->basicFallbackSanitize($content);
        }

        try {
            $config = \HTMLPurifier_Config::createDefault();
            
            // Only allow basic formatting tags
            $config->set('HTML.Allowed', 'b,strong,i,em,u,br,p,ul,ol,li,code');
            
            // No attributes allowed for maximum security
            $config->set('HTML.AllowedAttributes', '');
            
            // Disable auto-formatting to preserve user's intended formatting
            $config->set('AutoFormat.AutoParagraph', false);
            $config->set('AutoFormat.Linkify', false);
            $config->set('AutoFormat.RemoveEmpty', false);
            
            // Preserve newlines in the source
            $config->set('Core.NormalizeNewlines', false);
            $config->set('Core.CollectErrors', false);
            
            $purifier = new \HTMLPurifier($config);
            $cleaned = $purifier->purify($content);
            
            // DON'T convert newlines to <br> tags here - store raw content
            // The conversion will happen only when displaying to users
            return $cleaned;
            
        } catch (\Exception $e) {
            Log::error('HTMLPurifier error: ' . $e->getMessage());
            return $this->basicFallbackSanitize($content);
        }
    }

    /**
     * Basic fallback sanitization if HTML Purifier is not available
     * Still provides good security but not as comprehensive as HTML Purifier
     */
    private function basicFallbackSanitize($content)
    {
        // Remove NULL bytes
        $content = str_replace("\0", '', $content);
        
        // Convert all special characters to HTML entities
        $content = htmlspecialchars($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        // Define allowed tags and their safe replacements
        $allowedTags = [
            'b' => '&lt;b&gt;',
            'strong' => '&lt;strong&gt;',
            'i' => '&lt;i&gt;', 
            'em' => '&lt;em&gt;',
            'u' => '&lt;u&gt;',
            'br' => '&lt;br&gt;',
            'p' => '&lt;p&gt;',
            'ul' => '&lt;ul&gt;',
            'ol' => '&lt;ol&gt;',
            'li' => '&lt;li&gt;',
            'code' => '&lt;code&gt;'
        ];
        
        $closingTags = [
            'b' => '&lt;/b&gt;',
            'strong' => '&lt;/strong&gt;',
            'i' => '&lt;/i&gt;',
            'em' => '&lt;/em&gt;',
            'u' => '&lt;/u&gt;',
            'p' => '&lt;/p&gt;',
            'ul' => '&lt;/ul&gt;',
            'ol' => '&lt;/ol&gt;',
            'li' => '&lt;/li&gt;',
            'code' => '&lt;/code&gt;'
        ];
        
        // Restore allowed opening tags
        foreach ($allowedTags as $tag => $entity) {
            $content = str_replace($entity, "<$tag>", $content);
        }
        
        // Restore allowed closing tags
        foreach ($closingTags as $tag => $entity) {
            $content = str_replace($entity, "</$tag>", $content);
        }
        
        // DON'T convert newlines to <br> tags here either
        return $content;
    }

    /**
     * Process parts with image uploads
     */
    private function processPartsWithImages(Request $request, array $parts, array $existingParts = []): array
    {
        $processedParts = [];

        foreach ($parts as $index => $part) {
            $processedPart = [
                'title' => $part['title'] ?? '',
                'explanation' => $part['explanation'] ?? '',
                'image' => null,
            ];

            // Check if a new image was uploaded for this part
            if ($request->hasFile("part_images.{$index}")) {
                $image = $request->file("part_images.{$index}");
                $imageName = 'topic_part_' . time() . '_' . $index . '.' . $image->extension();
                $image->storeAs('public/topic-images', $imageName);
                $processedPart['image'] = asset('storage/topic-images/' . $imageName);

                // Delete old image if exists
                if (!empty($part['existing_image'])) {
                    $oldFilename = basename($part['existing_image']);
                    Storage::delete('public/topic-images/' . $oldFilename);
                }
            } elseif (!empty($part['existing_image'])) {
                // Keep existing image if no new one uploaded
                $processedPart['image'] = $part['existing_image'];
            }

            // Only add part if it has meaningful content
            if (!empty($processedPart['title']) || !empty($processedPart['explanation']) || !empty($processedPart['image'])) {
                $processedParts[] = $processedPart;
            }
        }

        return $processedParts;
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