<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * ContentSanitizationService
 *
 * Handles HTML content sanitization and part/image processing for topics.
 * Uses HTML Purifier when available, with a basic fallback sanitizer.
 */
class ContentSanitizationService
{
    /**
     * Most Secure: Using HTML Purifier
     * Protects against all known XSS attacks while allowing basic formatting
     */
    public function sanitizeWithHtmlPurifier($content)
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
    public function basicFallbackSanitize($content)
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
    public function processPartsWithImages(Request $request, array $parts, array $existingParts = []): array
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
}
