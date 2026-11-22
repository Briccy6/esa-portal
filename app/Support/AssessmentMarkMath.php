<?php

namespace App\Support;

use Illuminate\Support\Collection;

/**
 * Shared rules for TVET-style report cards:
 * - Tests and assignments: pool to one score out of 100 as sum(marks) / sum(max) * 100.
 * - Exam rows: each row is already on its own scale; convert to % then average rows (exams are not pooled like tests).
 * - Lesson total: (test% + exam%) / 2 when both exist; otherwise the non-zero part.
 */
class AssessmentMarkMath
{
    /**
     * @param  iterable<int, \App\Models\ReportCardTestMark>  $testMarks
     */
    public static function pooledTestPercentFromReportCardTestMarks(iterable $testMarks): float
    {
        $sum = 0.0;
        $maxSum = 0.0;
        foreach ($testMarks as $m) {
            $sum += (float) $m->original_mark;
            $max = (float) $m->max_mark > 0 ? (float) $m->max_mark : 100.0;
            $maxSum += $max;
        }

        return $maxSum > 0 ? round(min(100.0, ($sum / $maxSum) * 100.0), 2) : 0.0;
    }

    /**
     * @param  Collection<int, \App\Models\Mark>  $testMarks
     */
    public static function pooledTestPercentFromMarks(Collection $testMarks): float
    {
        if ($testMarks->isEmpty()) {
            return 0.0;
        }
        $sumVal = 0.0;
        $sumMax = 0.0;
        foreach ($testMarks as $mark) {
            $sumVal += (float) $mark->value;
            $mofo = ($mark->marks_out_of && (float) $mark->marks_out_of > 0)
                ? (float) $mark->marks_out_of
                : 100.0;
            $sumMax += $mofo;
        }

        return $sumMax > 0 ? round(min(100.0, ($sumVal / $sumMax) * 100.0), 2) : 0.0;
    }

    /**
     * @param  Collection<int, \App\Models\Mark>  $examMarks
     */
    public static function aggregatedExamPercentFromMarks(Collection $examMarks): float
    {
        if ($examMarks->isEmpty()) {
            return 0.0;
        }

        $parts = [];
        foreach ($examMarks as $mark) {
            $outOf = 100.0;
            if ($mark->marks_out_of && (float) $mark->marks_out_of > 0) {
                $outOf = (float) $mark->marks_out_of;
            } elseif ($exam = $mark->exam) {
                $examTotal = (float) ($exam->total_marks ?? 0);
                if ($examTotal > 0) {
                    $outOf = $examTotal;
                }
            }
            $outOf = max(1.0, $outOf);
            $val = (float) $mark->value;
            $parts[] = min(100.0, ($val / $outOf) * 100.0);
        }

        return round(min(100.0, array_sum($parts) / count($parts)), 2);
    }

    public static function combinedLessonPercent(float $testPct, float $examPct): float
    {
        $t = min(100.0, max(0.0, $testPct));
        $e = min(100.0, max(0.0, $examPct));
        if ($t > 0 && $e > 0) {
            return round(($t + $e) / 2.0, 2);
        }
        if ($t > 0) {
            return round($t, 2);
        }
        if ($e > 0) {
            return round($e, 2);
        }

        return 0.0;
    }
}
