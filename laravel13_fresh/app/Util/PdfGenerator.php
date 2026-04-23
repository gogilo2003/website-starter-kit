<?php

namespace App\Util;

use Exception;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\Quote;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;

class PdfGenerator
{
    public function generateQuotePdf(Quote $quote)
    {
        try {
            // Create DomPDF instance
            $options = new Options();
            $options->set('chroot', base_path());
            $options->set('isRemoteEnabled', true);
            $options->set('isHtml5ParserEnabled', true);
            $options->set('tempDir', storage_path('temp'));
            $options->set('fontDir', storage_path('fonts/'));
            $options->set('fontCache', storage_path('fonts/'));

            $dompdf = new Dompdf($options);

            // Render HTML
            $html = View::make('pdf.quote', [
                'quote' => $quote,
                'date' => now()->format('F d, Y'),
                'total' => $quote->total_amount,
            ])->render();
            $dompdf->loadHtml($html);

            // Set paper size and orientation
            $dompdf->setPaper('A4', 'portrait');

            // Render PDF
            $dompdf->render();

            return $dompdf;
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }

    public function saveQuotePdf(Quote $quote)
    {
        try {
            // Generate PDF
            $dompdf = $this->generateQuotePdf($quote);

            // Create directory if it doesn't exist
            $directory = storage_path('app/public/quotes');
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
            }

            // Generate filename and path
            $filename = 'quote-' . $quote->code . '.pdf';
            $relativePath = 'quotes/' . $filename;
            $fullPath = storage_path('app/public/' . $relativePath);
            // Save PDF to storage
            File::put($fullPath, $dompdf->output());

            // Return the relative path for database storage
            return $relativePath;
        } catch (Exception $e) {
            Log::error('PDF Generation Failed: ' . $e->getMessage());
            Log::error('Stack Trace: ' . $e->getTraceAsString());

            // Return a fallback path or null
            return null;
        }
    }
}
