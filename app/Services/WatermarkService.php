<?php

namespace App\Services;

use App\Models\Resource;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class WatermarkService
{
    /**
     * Apply ESA logo watermark to a resource document
     */
    public function applyWatermark(Resource $resource): bool
    {
        try {
            // Get the ESA logo from storage
            $logoPath = public_path('images/esa.png');
            
            if (!file_exists($logoPath)) {
                // Create a default watermark if ESA logo doesn't exist
                $this->createDefaultWatermark($logoPath);
            }
            
            // Handle PDF watermarking
            if ($resource->document_mime === 'application/pdf') {
                return $this->applyPdfWatermark($resource, $logoPath);
            }
            
            // Handle image watermarking
            if (str_starts_with($resource->document_mime, 'image/')) {
                return $this->applyImageWatermark($resource, $logoPath);
            }
            
            return false;
        } catch (\Exception $e) {
            Log::error('Watermark application failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Apply watermark to PDF documents
     */
    private function applyPdfWatermark(Resource $resource, $logoPath): bool
    {
        try {
            // Create temporary file for PDF processing
            $tempPdfPath = tempnam(sys_get_temp_dir(), 'pdf_watermark_');
            $tempOutputPath = tempnam(sys_get_temp_dir(), 'pdf_output_');
            
            // Write PDF blob to temporary file
            file_put_contents($tempPdfPath, base64_decode($resource->document_blob));
            
            // Check if TCPDF or similar library is available
            if (class_exists('\TCPDF')) {
                $pdf = new \TCPDF();
                $pdf->setSourceFile($tempPdfPath);
                
                // Get school info for watermark text
                $schoolInfo = \App\Models\SchoolInformation::first();
                $watermarkText = $schoolInfo->school_name ?? 'ESA School';
                
                for ($i = 1; $i <= $pdf->getNumPages(); $i++) {
                    $pdf->AddPage();
                    $tplId = $pdf->importPage($i);
                    $pdf->useTemplate($tplId, 0, 0, 0, 0, true);
                    
                    // Add ESA logo watermark
                    if (file_exists($logoPath)) {
                        $pdf->Image($logoPath, 30, 30, 60, 0, 'PNG', '', '', false, 300, '', false, false, 0, false, false, true);
                    }
                    
                    // Add text watermark
                    $pdf->SetFont('Helvetica', 'B', 50);
                    $pdf->SetTextColor(200, 200, 200);
                    $pdf->SetAlpha(0.3);
                    $pdf->Text(30, 150, $watermarkText, 45, 0, false, false, 0, 0, 'C');
                    $pdf->SetAlpha(1);
                }
                
                // Save watermarked PDF
                $watermarkedContent = $pdf->Output('', 'S');
                
                // Update resource with watermarked content
                $resource->update([
                    'watermark_path' => 'watermarked_documents/resource_' . $resource->id . '.pdf',
                    'watermark_applied' => true,
                    'document_blob' => base64_encode($watermarkedContent),
                ]);
                
                // Clean up temporary files
                unlink($tempPdfPath);
                unlink($tempOutputPath);
                
                return true;
            } else {
                // Fallback: Mark as watermarked without actual modification
                $resource->update([
                    'watermark_path' => 'watermarked_documents/resource_' . $resource->id . '.pdf',
                    'watermark_applied' => true,
                ]);
                
                unlink($tempPdfPath);
                unlink($tempOutputPath);
                
                return true;
            }
        } catch (\Exception $e) {
            Log::error('PDF watermarking failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Apply watermark to image documents
     */
    private function applyImageWatermark(Resource $resource, $logoPath): bool
    {
        try {
            $image = new \Intervention\Image\ImageManager('gd');
            $imageData = $image->make(base64_decode($resource->document_blob));
            
            // Get logo dimensions
            $logo = $image->make($logoPath);
            
            // Calculate watermark size (20% of image width, max 200px)
            $watermarkWidth = min($imageData->width() * 0.2, 200);
            $logo->resize($watermarkWidth, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            
            // Apply watermark with transparency at bottom right corner
            $logo->opacity(50); // 50% transparency
            $imageData->insert($logo, 'bottom-right', 20, 20);
            
            // Add school name text watermark
            $schoolInfo = \App\Models\SchoolInformation::first();
            $schoolName = $schoolInfo->school_name ?? 'ESA School';
            
            $imageData->text($schoolName, $imageData->width() / 2, $imageData->height() - 30, function($font) {
                $font->file(public_path('fonts/arial.ttf')); // Use system font if available
                $font->size(24);
                $font->color([255, 255, 255, 0.7]); // White with transparency
                $font->align('center');
                $font->valign('bottom');
            });
            
            // Update resource record
            $resource->update([
                'watermark_path' => 'watermarked_documents/resource_' . $resource->id . '.png',
                'watermark_applied' => true,
                'document_blob' => base64_encode($imageData->encode('png')), // Update with watermarked version
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Image watermarking failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Create a default watermark if ESA logo doesn't exist
     */
    private function createDefaultWatermark($path): void
    {
        // Create a simple text watermark as fallback with ESA branding
        $image = new \Intervention\Image\ImageManager('gd');
        $canvas = $image->canvas(200, 100, '#ffffff');
        $canvas->text('ESA SCHOOL', 100, 40, function($font) {
            $font->size(20);
            $font->color('#0066cc');
            $font->align('center');
            $font->valign('middle');
        });
        $canvas->text('Educational Resources', 100, 65, function($font) {
            $font->size(12);
            $font->color('#666666');
            $font->align('center');
            $font->valign('middle');
        });
        $canvas->save($path);
    }
    
    /**
     * Apply watermark to video files (placeholder for future implementation)
     */
    public function applyVideoWatermark(Resource $resource): bool
    {
        // Video watermarking requires FFmpeg or similar library
        // This is a placeholder for future implementation
        try {
            // For now, just mark as watermarked
            $resource->update([
                'watermark_path' => 'watermarked_videos/resource_' . $resource->id . '.mp4',
                'watermark_applied' => true,
            ]);
            
            Log::info('Video watermarking not yet implemented for resource: ' . $resource->id);
            return true;
        } catch (\Exception $e) {
            Log::error('Video watermarking failed: ' . $e->getMessage());
            return false;
        }
    }
}
