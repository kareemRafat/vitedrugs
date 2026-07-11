<?php

namespace App\Services\Imports;

class PdfTextExtractor
{
    public function extract(string $pdfPath): string
    {
        return (string) shell_exec(
            'pdftotext "' . $pdfPath . '" -'
        );
    }
}