<?php

use Barryvdh\DomPDF\Facade\Pdf;
use Tests\TestCase;

uses(TestCase::class);

test('pdf generation works', function () {
    try {
        // Test basic PDF generation
        $pdf = Pdf::loadHTML('<h1>Test PDF</h1><p>This is a test PDF to verify the dompdf package is working correctly.</p>');
        
        // Get the PDF content
        $content = $pdf->output();
        
        // Check if PDF content is generated (should be binary data)
        $this->assertNotEmpty($content);
        $this->assertIsString($content);
        
        // PDF files typically start with %PDF-
        $this->assertStringStartsWith('%PDF-', $content);
        
        echo "PDF generation test passed!\n";
        
    } catch (Exception $e) {
        $this->fail('PDF generation failed: ' . $e->getMessage());
    }
});
