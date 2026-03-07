<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Barryvdh\DomPDF\Facade\Pdf;

class TestPdfCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-pdf-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test PDF generation functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->info('Testing PDF generation...');
            
            // Test basic PDF generation
            $pdf = Pdf::loadHTML('<h1>Test PDF</h1><p>This is a test PDF to verify the dompdf package is working correctly.</p><p>Generated at: ' . now() . '</p>');
            
            // Save to file to test
            $filename = 'test_pdf_' . time() . '.pdf';
            $pdf->save(base_path($filename));
            
            $this->info("PDF generated successfully! File saved as: {$filename}");
            
            // Get the PDF content to verify it's valid
            $content = $pdf->output();
            
            // Check if PDF content is generated (should be binary data)
            if (empty($content)) {
                $this->error('PDF content is empty!');
                return 1;
            }
            
            // PDF files typically start with %PDF-
            if (!str_starts_with($content, '%PDF-')) {
                $this->error('Generated content does not appear to be a valid PDF!');
                return 1;
            }
            
            $this->info('PDF generation test passed! The PDF facade is working correctly.');
            return 0;
            
        } catch (Exception $e) {
            $this->error('PDF generation failed: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return 1;
        }
    }
}
