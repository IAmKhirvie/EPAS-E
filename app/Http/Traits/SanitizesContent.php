<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\Log;

trait SanitizesContent
{
    /**
     * Sanitize HTML content to prevent XSS attacks while allowing basic formatting
     * Allowed tags: b, strong, i, em, u, br, p, ul, ol, li, code
     *
     * @param string|null $content
     * @return string|null
     */
    protected function sanitizeContent(?string $content): ?string
    {
        if (empty($content)) {
            return $content;
        }

        // Try HTML Purifier first for best security
        if (class_exists('HTMLPurifier')) {
            return $this->sanitizeWithHtmlPurifier($content);
        }

        // Fallback to basic sanitization
        return $this->basicSanitize($content);
    }

    /**
     * Sanitize content using HTML Purifier (most secure)
     */
    private function sanitizeWithHtmlPurifier(string $content): string
    {
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
            return $purifier->purify($content);

        } catch (\Exception $e) {
            Log::error('HTMLPurifier error: ' . $e->getMessage());
            return $this->basicSanitize($content);
        }
    }

    /**
     * Basic fallback sanitization if HTML Purifier is not available
     */
    private function basicSanitize(string $content): string
    {
        // Remove NULL bytes
        $content = str_replace("\0", '', $content);

        // Convert all special characters to HTML entities
        $content = htmlspecialchars($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Define allowed tags and their safe replacements
        $allowedTags = [
            '&lt;b&gt;' => '<b>',
            '&lt;/b&gt;' => '</b>',
            '&lt;strong&gt;' => '<strong>',
            '&lt;/strong&gt;' => '</strong>',
            '&lt;i&gt;' => '<i>',
            '&lt;/i&gt;' => '</i>',
            '&lt;em&gt;' => '<em>',
            '&lt;/em&gt;' => '</em>',
            '&lt;u&gt;' => '<u>',
            '&lt;/u&gt;' => '</u>',
            '&lt;br&gt;' => '<br>',
            '&lt;br/&gt;' => '<br>',
            '&lt;br /&gt;' => '<br>',
            '&lt;p&gt;' => '<p>',
            '&lt;/p&gt;' => '</p>',
            '&lt;ul&gt;' => '<ul>',
            '&lt;/ul&gt;' => '</ul>',
            '&lt;ol&gt;' => '<ol>',
            '&lt;/ol&gt;' => '</ol>',
            '&lt;li&gt;' => '<li>',
            '&lt;/li&gt;' => '</li>',
            '&lt;code&gt;' => '<code>',
            '&lt;/code&gt;' => '</code>',
        ];

        // Restore allowed tags
        foreach ($allowedTags as $escaped => $original) {
            $content = str_ireplace($escaped, $original, $content);
        }

        return $content;
    }

    /**
     * Sanitize an array of content fields
     *
     * @param array $data
     * @param array $fields Fields to sanitize
     * @return array
     */
    protected function sanitizeFields(array $data, array $fields): array
    {
        foreach ($fields as $field) {
            if (isset($data[$field]) && is_string($data[$field])) {
                $data[$field] = $this->sanitizeContent($data[$field]);
            }
        }
        return $data;
    }

    /**
     * Strip all HTML tags (for plain text fields)
     */
    protected function stripHtml(?string $content): ?string
    {
        if (empty($content)) {
            return $content;
        }

        return strip_tags($content);
    }
}
