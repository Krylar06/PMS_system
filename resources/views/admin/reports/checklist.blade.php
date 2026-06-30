@extends('admin.layouts.app')

@section('title', 'Checklist Form')
@section('page_title', 'Checklist Form')

@section('content')
<style>
    @media print {
        aside, header, .no-print { display: none !important; }
        .lg\:ml-64 { margin-left: 0 !important; }
        main { padding: 0 !important; }
        body { background: #ffffff !important; }
        .print-card { border: none !important; box-shadow: none !important; }
        table { font-size: 11px !important; }
        th, td { padding: 6px !important; }
    }
</style>

<div class="space-y-5">
    <div class="no-print flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Checklist Form</h1>
            <p class="mt-1 text-sm text-gray-500">Printable add-on checklist generated from filtered devices.</p>
        </div>
        <div class="flex gap-2">
            <button onclick="window.print()" class="inline-flex items-center rounded-xl bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-black">Print Checklist</button>
            <a href="{{ route('admin.reports.index') }}" class="inline-flex items-center rounded-xl bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200">Back to Reports</a>
        </div>
    </div>

    <div class="no-print rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
        <form method="GET" class="grid grid-cols-1 gap-3 lg:grid-cols-5">
            <input name="q" value="{{ $q }}" placeholder="Search property #, serial #, brand..." class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">

            <select name="type_id" class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                <option value="">All device types</option>
                @foreach($types as $type)
                    <option value="{{ $type->id }}" @selected((int) $selectedTypeId === $type->id)>{{ $type->name }}</option>
                @endforeach
            </select>

            <select name="college_id" class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                <option value="">All colleges</option>
                @foreach($colleges as $college)
                    <option value="{{ $college->id }}" @selected((int) $selectedCollegeId === $college->id)>{{ $college->name }}</option>
                @endforeach
            </select>

            <select name="office_id" class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                <option value="">All offices</option>
                @foreach($offices as $office)
                    <option value="{{ $office->id }}" @selected((int) $selectedOfficeId === $office->id)>{{ $office->name }} @if($office->college) — {{ $office->college->name }} @endif</option>
                @endforeach
            </select>

            <div class="flex gap-2">
                <button type="submit" class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Generate</button>
                <a href="{{ route('admin.reports.checklist') }}" class="rounded-xl bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200">Reset</a>
            </div>
        </form>
    </div>

    <div class="print-card rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
        <div class="mb-5 text-center">
            <h2 class="text-lg font-bold uppercase tracking-wide text-gray-900">Equipment Preventive Maintenance Checklist</h2>
            <p class="mt-1 text-sm text-gray-600">Generated: {{ $generatedAt->format('F d, Y h:i A') }}</p>
            <p class="mt-1 text-sm text-gray-600">Total Devices: {{ number_format($devices->count()) }}</p>
        </div>

        <div class="mb-4 grid grid-cols-1 gap-3 text-sm md:grid-cols-3">
            <div><span class="font-semibold">Checked By:</span> ________________________</div>
            <div><span class="font-semibold">Date Checked:</span> ________________________</div>
            <div><span class="font-semibold">Office:</span> ________________________</div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-300 text-left text-xs">
                <thead class="bg-gray-100 uppercase tracking-wide text-gray-600">
                    <tr>
                        <th class="border border-gray-300 px-2 py-2">No.</th>
                        <th class="border border-gray-300 px-2 py-2">Type</th>
                        <th class="border border-gray-300 px-2 py-2">Property #</th>
                        <th class="border border-gray-300 px-2 py-2">Serial #</th>
                        <th class="border border-gray-300 px-2 py-2">Assigned To</th>
                        <th class="border border-gray-300 px-2 py-2">Office</th>
                        <th class="border border-gray-300 px-2 py-2">Cleaned</th>
                        <th class="border border-gray-300 px-2 py-2">Functional</th>
                        <th class="border border-gray-300 px-2 py-2">With Add-ons / Accessories</th>
                        <th class="border border-gray-300 px-2 py-2">Needs Repair</th>
                        <th class="border border-gray-300 px-2 py-2">Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($devices as $device)
                        @php
                            $assignment = $device->currentAssignment;
                            $staff = $assignment?->staff;
                            $office = $staff?->office;
                            $staffName = $staff ? trim(($staff->last_name ?? '') . ', ' . ($staff->first_name ?? '')) : '';
                        @endphp
                        <tr>
                            <td class="border border-gray-300 px-2 py-2 text-center">{{ $loop->iteration }}</td>
                            <td class="border border-gray-300 px-2 py-2">{{ $device->type?->name ?? '-' }}</td>
                            <td class="border border-gray-300 px-2 py-2">{{ $device->property_number }}</td>
                            <td class="border border-gray-300 px-2 py-2">{{ $device->serial_number ?: '-' }}</td>
                            <td class="border border-gray-300 px-2 py-2">{{ $staffName ?: '-' }}</td>
                            <td class="border border-gray-300 px-2 py-2">{{ $office?->name ?? '-' }}</td>
                            <td class="border border-gray-300 px-2 py-2 text-center">☐</td>
                            <td class="border border-gray-300 px-2 py-2 text-center">☐</td>
                            <td class="border border-gray-300 px-2 py-2 text-center">☐</td>
                            <td class="border border-gray-300 px-2 py-2 text-center">☐</td>
                            <td class="border border-gray-300 px-2 py-2">&nbsp;</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="border border-gray-300 px-4 py-8 text-center text-gray-500">No devices found for this checklist.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-8 grid grid-cols-1 gap-8 text-sm md:grid-cols-3">
            <div>Prepared by: ________________________</div>
            <div>Verified by: ________________________</div>
            <div>Approved by: ________________________</div>
        </div>
    </div>
</div>
@endsection
