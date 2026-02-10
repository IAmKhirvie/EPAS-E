<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Smalot\PdfParser\Parser as PdfParser;
use Maatwebsite\Excel\Facades\Excel;

class FileParsingService
{
    /**
     * Extract text content from uploaded file (PDF or Excel).
     */
    public function extractText(string $filePath, string $mimeType): ?string
    {
        try {
            if (str_contains($mimeType, 'pdf')) {
                return $this->extractFromPdf($filePath);
            }

            if (str_contains($mimeType, 'spreadsheet') ||
                str_contains($mimeType, 'excel') ||
                str_contains($mimeType, 'ms-excel')) {
                return $this->extractFromExcel($filePath);
            }

            return null;
        } catch (\Exception $e) {
            Log::error("File parsing failed: " . $e->getMessage(), [
                'file_path' => $filePath,
                'mime_type' => $mimeType,
            ]);
            return null;
        }
    }

    protected function extractFromPdf(string $filePath): ?string
    {
        $parser = new PdfParser();
        $pdf = $parser->parseFile($filePath);
        $text = trim($pdf->getText());
        return $text ?: null;
    }

    protected function extractFromExcel(string $filePath): ?string
    {
        $data = Excel::toArray([], $filePath);
        $text = '';
        foreach ($data as $sheet) {
            foreach ($sheet as $row) {
                $line = implode(' | ', array_filter($row, fn($v) => $v !== null && $v !== ''));
                if ($line) {
                    $text .= $line . "\n";
                }
            }
        }
        return trim($text) ?: null;
    }
}
