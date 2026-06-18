<?php

namespace App\Services;

use App\Models\AcademicYear;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Session;

class AcademicYearContext
{
    public const SESSION_KEY = 'admin_academic_year_id';

    public static function current(): ?AcademicYear
    {
        return AcademicYear::ongoing()->orderByDesc('starting_date')->first()
            ?? AcademicYear::orderByDesc('starting_date')->first();
    }

    public static function selected(): ?AcademicYear
    {
        $id = Session::get(self::SESSION_KEY);

        if ($id) {
            $year = AcademicYear::find($id);
            if ($year) {
                return $year;
            }
        }

        return self::current();
    }

    public static function selectedId(): ?int
    {
        return self::selected()?->id;
    }

    public static function setSelected(int $academicYearId): void
    {
        Session::put(self::SESSION_KEY, $academicYearId);
    }

    public static function isViewingPastYear(): bool
    {
        $current = self::current();
        $selected = self::selected();

        return $current && $selected && $current->id !== $selected->id;
    }

    public static function allOrdered()
    {
        return AcademicYear::orderByDesc('starting_date')->get();
    }

    public static function filterStudents(Builder $query): Builder
    {
        $id = self::selectedId();

        return $id ? $query->where('academic_year_id', $id) : $query;
    }

    public static function filterTransactions(Builder $query): Builder
    {
        $id = self::selectedId();

        return $id
            ? $query->whereHas('student', fn (Builder $q) => $q->where('academic_year_id', $id))
            : $query;
    }

    public static function filterExpenses(Builder $query): Builder
    {
        $year = self::selected();

        if (! $year) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($year) {
            $q->whereHas('term', fn (Builder $t) => $t->where('academic_year_id', $year->id))
                ->orWhereBetween('expense_date', [
                    $year->starting_date->format('Y-m-d'),
                    $year->expiry_date->format('Y-m-d'),
                ]);
        });
    }

    public static function filterCourses(Builder $query): Builder
    {
        $id = self::selectedId();

        return $id ? $query->where('academic_year_id', $id) : $query;
    }
}
