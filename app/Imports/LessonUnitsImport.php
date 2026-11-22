<?php

namespace App\Imports;

use App\Models\LessonUnit;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Validator;

class LessonUnitsImport implements ToModel, WithHeadingRow
{
    protected $lessonId;

    public function __construct($lessonId)
    {
        $this->lessonId = $lessonId;
    }

    public function model(array $row)
    {
        // Validate row data
        $validator = Validator::make($row, [
            'unit_name' => 'required|string|max:255',
            'hours' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            throw new \Exception('Invalid data in row: ' . $validator->errors()->first());
        }

        return new LessonUnit([
            'lesson_id' => $this->lessonId,
            'unit_name' => $row['unit_name'],
            'hours' => $row['hours'],
            'file_path' => $row['file_path'] ?? null,
        ]);
    }
}
