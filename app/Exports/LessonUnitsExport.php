<?php

namespace App\Exports;

use App\Models\LessonUnit;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LessonUnitsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $lessonId;

    public function __construct($lessonId)
    {
        $this->lessonId = $lessonId;
    }

    public function collection()
    {
        return LessonUnit::where('lesson_id', $this->lessonId)->get();
    }

    public function headings(): array
    {
        return [
            'Unit Name',
            'Hours',
            'File Path',
        ];
    }

    public function map($unit): array
    {
        return [
            $unit->unit_name,
            $unit->hours,
            $unit->file_path ?? '',
        ];
    }
}
