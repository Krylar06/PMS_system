@extends('admin.layouts.app')

@section('title', 'Checked Equipment Report')
@section('page_title', 'Checked Equipment Report')
@section('breadcrumbs')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600">Dashboard</a>
    <span>/</span>
    <a href="{{ route('admin.reports.index') }}" class="hover:text-blue-600">Reports</a>
    <span>/</span>
    <span class="font-medium text-gray-800">Registered Accounts</span>
@endsection

@section('content')
<div class="space-y-5">
    <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Checked Equipment Report</h1>
            <p class="mt-1 text-sm text-gray-500">Equipment marked checked through the maintenance checklist.</p>
        </div>
        <a href="{{ route('admin.reports.index') }}" class="inline-flex items-center rounded-xl bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200">Back to Reports</a>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        @forelse(($checkerSummary ?? $adminSummary)->take(3) as $summary)
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <div class="text-sm font-medium text-gray-500">{{ $summary->checkedBy?->name ?? 'Unknown User' }}</div>
                <div class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($summary->total) }}</div>
                <div class="mt-1 text-xs uppercase tracking-wide text-gray-400">Marked checked</div>
            </div>
        @empty
            <div class="rounded-2xl border border-gray-200 bg-white p-5 text-sm text-gray-500 shadow-sm md:col-span-3">
                No checked-equipment records yet.
            </div>
        @endforelse
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
        <form method="GET" class="grid grid-cols-1 gap-3 lg:grid-cols-6">
            <input name="q" value="{{ $q }}" placeholder="Search property #, remarks..." class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">

            <select name="checker_id" class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                <option value="">All checked by</option>
                @foreach(($checkerUsers ?? $adminUsers) as $checker)
                    <option value="{{ $checker->id }}" @selected((int)($checkerId ?? $adminId ?? 0) === $checker->id)>{{ $checker->name }}</option>
                @endforeach
            </select>

            <select name="type_id" class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                <option value="">All device types</option>
                @foreach($types as $type)
                    <option value="{{ $type->id }}" @selected((int) $typeId === $type->id)>{{ $type->name }}</option>
                @endforeach
            </select>

            <input type="date" name="date_from" value="{{ $dateFrom }}" class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
            <input type="date" name="date_to" value="{{ $dateTo }}" class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">

            <div class="flex gap-2">
                <button type="submit" class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Generate</button>
                <a href="{{ route('admin.reports.checkedEquipment') }}" class="rounded-xl bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200">Reset</a>
            </div>
        </form>
    </div>

    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4">
            <div>
                <h2 class="font-semibold text-gray-900">Marked Checked Records</h2>
                <p class="mt-1 text-sm text-gray-500">{{ number_format($records->total()) }} result(s)</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3">Checked By</th>
                        <th class="px-4 py-3">Device</th>
                        <th class="px-4 py-3">Type</th>
                        <th class="px-4 py-3">Office / College</th>
                        <th class="px-4 py-3">Remarks</th>
                        <th class="px-4 py-3">PDF</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($records as $record)
                        @php
                            $device = $record->device;
                            $assignment = $device?->currentAssignment;
                            $office = $assignment?->staff?->office;
                            $college = $office?->college;
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-700">{{ $record->maintenance_date ? $record->maintenance_date->format('M d, Y') : '-' }}</td>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $record->checkedBy?->name ?? '-' }}</td>
                            <td class="px-4 py-3">
                                @if($device)
                                    <a href="{{ route('admin.devices.show', $device) }}" class="font-medium text-blue-700 hover:underline">{{ $device->property_number }}</a>
                                    <div class="text-xs text-gray-500">SN: {{ $device->serial_number ?: '-' }}</div>
                                @else
                                    <span class="text-gray-400">Device deleted</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-700">{{ $device?->type?->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-700">
                                {{ $office?->name ?? '-' }}
                                @if($college)
                                    <div class="text-xs text-gray-500">{{ $college->name }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-700">{{ $record->remarks ?: '-' }}</td>
                            <td class="px-4 py-3">
                                @if($device)
                                    <a
                                        href="{{ route('admin.reports.checkedEquipment.pdf', $record) }}"
                                        target="_blank"
                                        rel="noopener"
                                        class="rounded-lg bg-gray-900 px-3 py-1.5 text-sm font-medium text-white hover:bg-black"
                                    >
                                        PDF
                                    </a>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-6 py-8 text-center text-gray-500">No records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-gray-200 px-5 py-4">
            {{ $records->links() }}
        </div>
    </div>
</div>
@endsection
