<?php

namespace App\Exports;

use App\Models\Teacher;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TeachersExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Teacher::with('courses')->get();
    }

    /**
     * Define the headings for the Excel file
     */
    public function headings(): array
    {
        return [
            'ID',
            'Teacher Name',
            'Teacher ID Number',
            'National ID Number',
            'Email',
            'Phone',
            'Department',
            'Employment Type',
            'Education Level',
            'Salary (RWF)',
            'Assigned Courses',
            'Total Courses',
            'Created At',
            'Updated At'
        ];
    }

    /**
     * Map the data for each row
     */
    public function map($teacher): array
    {
        return [
            $teacher->id,
            $teacher->teacher_name,
            $teacher->teacher_id_number,
            $teacher->id_number,
            $teacher->email,
            $teacher->telephone,
            $teacher->department,
            $teacher->employment_type,
            $teacher->level_of_education,
            number_format($teacher->salary, 2),
            $teacher->courses->pluck('name')->implode(', '),
            $teacher->courses->count(),
            $teacher->created_at ? $teacher->created_at->format('Y-m-d H:i:s') : '',
            $teacher->updated_at ? $teacher->updated_at->format('Y-m-d H:i:s') : ''
        ];
    }

    /**
     * Apply styles to the worksheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as header
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => [
                        'argb' => 'FFE2E2E2',
                    ],
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ],
            // Style for all data rows
            'A:N' => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ],
        ];
    }

    /**
     * Set column widths
     */
    public function columnWidths(): array
    {
        return [
            'A' => 5,   // ID
            'B' => 25,  // Teacher Name
            'C' => 20,  // Teacher ID Number
            'D' => 20,  // National ID Number
            'E' => 30,  // Email
            'F' => 15,  // Phone
            'G' => 20,  // Department
            'H' => 15,  // Employment Type
            'I' => 25,  // Education Level
            'J' => 15,  // Salary
            'K' => 40,  // Assigned Courses
            'L' => 12,  // Total Courses
            'M' => 20,  // Created At
            'N' => 20,  // Updated At
        ];
    }

    /**
     * Set the title of the worksheet
     */
    public function title(): string
    {
        return 'Teachers Data';
    }
}