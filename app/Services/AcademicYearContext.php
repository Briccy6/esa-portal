<?php

namespace App\Services;

use App\Models\AcademicYear;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AcademicYearContext
{
    public const SESSION_KEY = 'admin_academic_year_id';
    private const CACHE_PREFIX = 'academic_year_context.';

    public static function current(): ?AcademicYear
    {
        return self::rememberForRequest('current', function () {
            $today = now()->toDateString();

            return self::allOrdered()->first(function (AcademicYear $year) use ($today) {
                return $year->starting_date
                    && $year->expiry_date
                    && $year->starting_date->toDateString() <= $today
                    && $year->expiry_date->toDateString() >= $today;
            }) ?? self::allOrdered()->first();
        });
    }

    public static function selected(): ?AcademicYear
    {
        return self::rememberForRequest('selected', function () {
            $id = Session::get(self::SESSION_KEY);

            if ($id) {
                $year = self::allOrdered()->firstWhere('id', (int) $id);
                if ($year) {
                    return $year;
                }
            }

            return self::current();
        });
    }

    public static function selectedId(): ?int
    {
        return self::selected()?->id;
    }

    public static function setSelected(int $academicYearId): void
    {
        Session::put(self::SESSION_KEY, $academicYearId);
        self::forgetForRequest('selected');
    }

    public static function isViewingPastYear(): bool
    {
        $current = self::current();
        $selected = self::selected();

        return $current && $selected && $current->id !== $selected->id;
    }

    public static function allOrdered()
    {
        return self::rememberForRequest(
            'all_ordered',
            fn () => AcademicYear::orderByDesc('starting_date')->get()
        );
    }

    public static function filterStudents(Builder $query): Builder
    {
        $id = self::selectedId();

        return $id ? $query->where('students.academic_year_id', $id) : $query;
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

    private static function rememberForRequest(string $key, callable $resolver)
    {
        if (! app()->bound('request')) {
            return $resolver();
        }

        $request = app(Request::class);
        $attribute = self::CACHE_PREFIX . $key;

        if ($request->attributes->has($attribute)) {
            return $request->attributes->get($attribute);
        }

        $value = $resolver();
        $request->attributes->set($attribute, $value);

        return $value;
    }

    private static function forgetForRequest(string $key): void
    {
        if (app()->bound('request')) {
            app(Request::class)->attributes->remove(self::CACHE_PREFIX . $key);
        }
    }
}
