<?php

namespace App\Services;

use App\Models\Student;
use App\Models\ReportCardSetting;
use Carbon\Carbon;

class ReportCardService
{
    /**
     * Check if a student can access their report card
     * 
     * @param Student $student
     * @param int|array|null $termIds - Optional: specific term(s) to check. If null, checks all terms.
     * @return bool
     */
    public static function canAccessReportCard(Student $student, $termIds = null): bool
    {
        // Normalize term IDs to array
        $termIdsArray = null;
        if ($termIds !== null) {
            $termIdsArray = is_array($termIds) ? $termIds : [(int)$termIds];
        }
        
        // Check 1: Student must have paid all fees for the specified terms
        if (!FeePaymentService::hasPaidAllFees($student, $termIdsArray)) {
            return false;
        }
        
        // Check 2: Report cards must be available based on admin settings
        $setting = ReportCardSetting::getActive();
        
        if (!$setting) {
            return false; // No active setting configured
        }
        
        return $setting->isAvailable();
    }
    
    /**
     * Get report card access status for a student
     * 
     * @param Student $student
     * @param int|array|null $termIds - Optional: specific term(s) to check. If null, checks all terms.
     * @return array
     */
    public static function getAccessStatus(Student $student, $termIds = null): array
    {
        // Normalize term IDs to array
        $termIdsArray = null;
        if ($termIds !== null) {
            $termIdsArray = is_array($termIds) ? $termIds : [(int)$termIds];
        }
        
        $setting = ReportCardSetting::getActive();
        $paymentStatus = FeePaymentService::getPaymentStatus($student, $termIdsArray);
        
        $canAccess = false;
        $reason = '';
        $countdownData = null;
        
        // Check payment status
        if (!$paymentStatus['is_fully_paid']) {
            $reason = 'Fees not fully paid. Outstanding: ' . number_format($paymentStatus['outstanding']) . ' Rwf';
        } else if (!$setting) {
            $reason = 'Report card access not configured by administration';
        } else if (!$setting->isAvailable()) {
            $reason = 'Report cards not yet available';
            $countdownData = $setting->getCountdownData();
        } else {
            $canAccess = true;
            $reason = 'Report card available for download';
        }
        
        return [
            'can_access' => $canAccess,
            'reason' => $reason,
            'payment_status' => $paymentStatus,
            'setting' => $setting,
            'countdown_data' => $countdownData,
            'available_date' => $setting ? $setting->report_card_available_at : null
        ];
    }
    
    /**
     * Get countdown data for AJAX requests
     */
    public static function getCountdownData(): ?array
    {
        $setting = ReportCardSetting::getActive();
        
        if (!$setting || $setting->isAvailable()) {
            return null;
        }
        
        return $setting->getCountdownData();
    }
    
    /**
     * Format countdown data for display
     */
    public static function formatCountdown(array $countdownData): string
    {
        if (!$countdownData) {
            return 'Available now';
        }
        
        $parts = [];
        
        if ($countdownData['months'] > 0) {
            $parts[] = $countdownData['months'] . ' month' . ($countdownData['months'] > 1 ? 's' : '');
        }
        
        if ($countdownData['weeks'] > 0) {
            $parts[] = $countdownData['weeks'] . ' week' . ($countdownData['weeks'] > 1 ? 's' : '');
        }
        
        if ($countdownData['days'] > 0) {
            $parts[] = $countdownData['days'] . ' day' . ($countdownData['days'] > 1 ? 's' : '');
        }
        
        if ($countdownData['hours'] > 0) {
            $parts[] = $countdownData['hours'] . ' hour' . ($countdownData['hours'] > 1 ? 's' : '');
        }
        
        if ($countdownData['minutes'] > 0) {
            $parts[] = $countdownData['minutes'] . ' minute' . ($countdownData['minutes'] > 1 ? 's' : '');
        }
        
        if ($countdownData['seconds'] > 0 && empty($parts)) {
            $parts[] = $countdownData['seconds'] . ' second' . ($countdownData['seconds'] > 1 ? 's' : '');
        }
        
        return implode(', ', $parts);
    }
}
