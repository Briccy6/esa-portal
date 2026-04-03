@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8 px-4">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Report Card</h1>
                <button 
                    onclick="window.print()"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg flex items-center gap-2"
                >
                    <i class="fas fa-print"></i>
                    Print Report Card
                </button>
            </div>

            <div class="bg-green-50 border border-green-200 rounded-lg p-4 flex items-center gap-3">
                <i class="fas fa-check-circle text-green-600 text-xl"></i>
                <div>
                    <p class="font-semibold text-green-900">Access Granted</p>
                    <p class="text-sm text-green-700">All fees paid. You can now view your report card.</p>
                </div>
            </div>
        </div>

        <!-- Report Card Content -->
        <div class="bg-white border border-gray-300 rounded-lg shadow-lg p-8">
            <div class="grid grid-cols-2 gap-8 mb-8">
                <!-- Student Info -->
                <div>
                    <h2 class="text-sm font-bold text-gray-700 uppercase mb-4">Student Information</h2>
                    <div class="space-y-2 text-sm">
                        <div>
                            <p class="text-gray-600">Name</p>
                            <p class="font-semibold text-gray-900">{{ $student->full_name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Registration Number</p>
                            <p class="font-semibold text-gray-900">{{ $student->reg_number ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Email</p>
                            <p class="font-semibold text-gray-900">{{ $student->email ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Academic Info -->
                <div>
                    <h2 class="text-sm font-bold text-gray-700 uppercase mb-4">Academic Information</h2>
                    <div class="space-y-2 text-sm">
                        <div>
                            <p class="text-gray-600">Branch</p>
                            <p class="font-semibold text-gray-900">{{ $student->branch->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Course</p>
                            <p class="font-semibold text-gray-900">{{ $student->course->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Academic Year</p>
                            <p class="font-semibold text-gray-900">{{ $student->academicYear->year ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grades Table -->
            @if($reportCard ?? null)
            <div class="mb-8">
                <h2 class="text-sm font-bold text-gray-700 uppercase mb-4">Academic Performance</h2>
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-gray-100 border-b-2 border-gray-300">
                                <th class="border border-gray-300 p-3 text-left text-sm font-semibold text-gray-900">Subject</th>
                                <th class="border border-gray-300 p-3 text-center text-sm font-semibold text-gray-900">Score</th>
                                <th class="border border-gray-300 p-3 text-center text-sm font-semibold text-gray-900">Grade</th>
                                <th class="border border-gray-300 p-3 text-center text-sm font-semibold text-gray-900">Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reportCard as $record)
                            <tr class="border-b border-gray-200 hover:bg-gray-50">
                                <td class="border border-gray-300 p-3 text-sm text-gray-900">{{ $record->subject ?? 'N/A' }}</td>
                                <td class="border border-gray-300 p-3 text-center text-sm font-semibold text-gray-900">{{ $record->score ?? 'N/A' }}</td>
                                <td class="border border-gray-300 p-3 text-center text-sm font-semibold 
                                    @if(($record->grade ?? 'F') === 'A') text-green-600 @elseif(in_array($record->grade ?? 'F', ['B', 'C'])) text-blue-600 @else text-red-600 @endif
                                ">
                                    {{ $record->grade ?? 'N/A' }}
                                </td>
                                <td class="border border-gray-300 p-3 text-center text-sm text-gray-700">{{ $record->remarks ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="border border-gray-300 p-6 text-center text-gray-500">
                                    No grades available yet
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @else
            <div class="mb-8 p-6 bg-gray-50 border border-gray-200 rounded-lg text-center text-gray-600">
                <i class="fas fa-file-search text-4xl mb-4 text-gray-400"></i>
                <p>Report card data not yet available</p>
            </div>
            @endif

            <!-- Summary -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                <h2 class="text-sm font-bold text-gray-700 uppercase mb-4">Summary</h2>
                <div class="grid grid-cols-3 gap-4">
                    <div class="text-center">
                        <p class="text-gray-600 text-sm mb-1">Total Subjects</p>
                        <p class="text-2xl font-bold text-gray-900">{{ count($reportCard ?? []) }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-gray-600 text-sm mb-1">Average Score</p>
                        <p class="text-2xl font-bold text-blue-600">
                            {{ $reportCard ? round(array_sum(array_column($reportCard, 'score')) / count($reportCard), 2) : 'N/A' }}
                        </p>
                    </div>
                    <div class="text-center">
                        <p class="text-gray-600 text-sm mb-1">Date Generated</p>
                        <p class="text-sm font-semibold text-gray-900">{{ now()->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-8 text-center text-sm text-gray-500">
            <p>This is an electronically generated report card. For official purposes, please contact your school administration.</p>
            <p class="mt-2">Generated on {{ now()->format('F d, Y H:i A') }}</p>
        </div>
    </div>
</div>

<style>
    @media print {
        .container {
            max-width: 100%;
        }
        button {
            display: none;
        }
        .container > :first-child > .flex {
            display: none;
        }
    }
</style>
@endsection
