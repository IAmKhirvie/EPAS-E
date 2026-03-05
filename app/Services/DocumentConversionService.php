<?php

namespace App\Services;

use PhpOffice\PhpWord\IOFactory as WordIOFactory;
use PhpOffice\PhpPresentation\IOFactory as PresentationIOFactory;
use PhpOffice\PhpPresentation\Shape\RichText;
use PhpOffice\PhpPresentation\Shape\RichText\Run;
use Illuminate\Support\Facades\Log;

class DocumentConversionService
{
    /**
     * Convert a document file to HTML.
     *
     * @return string|null HTML content, or null if not convertible (e.g., PDF)
     */
    public function convertToHtml(string $filePath, string $extension): ?string
    {
        $extension = strtolower($extension);

        try {
            return match ($extension) {
                'docx', 'doc' => $this->convertWordToHtml($filePath),
                'pptx', 'ppt' => $this->convertPresentationToHtml($filePath),
                default => null,
            };
        } catch (\Exception $e) {
            Log::error('Document conversion failed', [
                'file' => $filePath,
                'extension' => $extension,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    protected function convertWordToHtml(string $filePath): ?string
    {
        $phpWord = WordIOFactory::load($filePath);
        $htmlWriter = WordIOFactory::createWriter($phpWord, 'HTML');

        ob_start();
        $htmlWriter->save('php://output');
        $html = ob_get_clean();

        // Extract just the body content
        if (preg_match('/<body[^>]*>(.*)<\/body>/is', $html, $matches)) {
            $html = $matches[1];
        }

        return $this->sanitizeHtml($html);
    }

    protected function convertPresentationToHtml(string $filePath): ?string
    {
        $presentation = PresentationIOFactory::load($filePath);
        $html = '';

        foreach ($presentation->getAllSlides() as $slideIndex => $slide) {
            $slideNum = $slideIndex + 1;
            $html .= "<div class=\"doc-slide\" style=\"margin-bottom: 1.5rem; padding: 1rem; border: 1px solid #e9ecef; border-radius: 8px;\">";
            $html .= "<h4 style=\"color: #6c757d; border-bottom: 1px solid #e9ecef; padding-bottom: 0.5rem;\">Slide {$slideNum}</h4>";

            foreach ($slide->getShapeCollection() as $shape) {
                if ($shape instanceof RichText) {
                    foreach ($shape->getParagraphs() as $paragraph) {
                        $paragraphHtml = '';
                        foreach ($paragraph->getRichTextElements() as $element) {
                            $text = method_exists($element, 'getText') ? e($element->getText()) : '';
                            if (empty(trim($text))) {
                                continue;
                            }
                            if ($element instanceof Run) {
                                $font = $element->getFont();
                                if ($font->isBold()) {
                                    $text = '<strong>' . $text . '</strong>';
                                }
                                if ($font->isItalic()) {
                                    $text = '<em>' . $text . '</em>';
                                }
                                if ($font->isUnderline()) {
                                    $text = '<u>' . $text . '</u>';
                                }
                            }
                            $paragraphHtml .= $text;
                        }
                        if (!empty(trim(strip_tags($paragraphHtml)))) {
                            $html .= '<p>' . $paragraphHtml . '</p>';
                        }
                    }
                }
            }

            $html .= '</div>';
        }

        return $this->sanitizeHtml($html);
    }

    public function sanitizeHtml(string $html): string
    {
        $config = \HTMLPurifier_Config::createDefault();
        $config->set('HTML.Allowed',
            'p,br,strong,b,em,i,u,h1,h2,h3,h4,h5,h6,ul,ol,li,table,thead,tbody,tr,th,td,div,span,hr,blockquote,pre,code,a[href],img[src|alt|width|height|style]'
        );
        $config->set('HTML.AllowedAttributes', 'class,style,src,alt,width,height,href');
        // Only use CSS properties that HTMLPurifier actually supports
        $config->set('CSS.AllowedProperties', 'color,background-color,font-size,font-weight,text-align,margin,padding,border,text-decoration,font-style,font-family');
        $config->set('Cache.DefinitionImpl', null);
        $config->set('URI.AllowedSchemes', ['http' => true, 'https' => true, 'data' => true]);

        $purifier = new \HTMLPurifier($config);
        return $purifier->purify($html);
    }
}
