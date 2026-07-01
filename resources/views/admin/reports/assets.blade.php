@extends('admin.layouts.app')

@section('title', 'All Assets Report')
@section('page_title', 'All Assets Report')

@section('breadcrumbs')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600">Dashboard</a>
    <span>/</span>
    <a href="{{ route('admin.reports.index') }}" class="hover:text-blue-600">Reports</a>
    <span>/</span>
    <span class="font-medium text-gray-800">All Assets</span>
@endsection

@section('content')
<div class="space-y-5">
    <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">All Assets Report</h1>
            <p class="mt-1 text-sm text-gray-500">Automatically filters by device type, college, office, or search text.</p>
        </div>

        <a
            href="{{ route('admin.reports.index') }}"
            class="no-print inline-flex items-center rounded-xl bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200"
        >
            Back to Reports
        </a>
    </div>

    <div class="no-print rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
        <form
            id="asset-filter-form"
            method="GET"
            class="grid grid-cols-1 gap-3 lg:grid-cols-5"
        >
            <input
                id="asset-search"
                name="q"
                value="{{ $q }}"
                placeholder="Search property #, serial #, brand..."
                autocomplete="off"
                class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
            >

            <select
                id="asset-type-filter"
                name="type_id"
                class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
            >
                <option value="">All device types</option>
                @foreach($types as $type)
                    <option value="{{ $type->id }}" @selected((int) $selectedTypeId === $type->id)>
                        {{ $type->name }}
                    </option>
                @endforeach
            </select>

            <select
                id="asset-college-filter"
                name="college_id"
                class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
            >
                <option value="">All colleges</option>
                @foreach($colleges as $college)
                    <option value="{{ $college->id }}" @selected((int) $selectedCollegeId === $college->id)>
                        {{ $college->code ? $college->code . ' — ' : '' }}{{ $college->name }}
                    </option>
                @endforeach
            </select>

            <select
                id="asset-office-filter"
                name="office_id"
                class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
            >
                <option value="">All offices</option>
                @foreach($offices as $office)
                    <option
                        value="{{ $office->id }}"
                        data-college-id="{{ $office->college_id }}"
                        @selected((int) $selectedOfficeId === $office->id)
                    >
                        {{ $office->name }} @if($office->college) — {{ $office->college->code ?: $office->college->name }} @endif
                    </option>
                @endforeach
            </select>

            <div class="flex gap-2">
                <a
                    href="{{ route('admin.reports.assets') }}"
                    class="inline-flex items-center rounded-xl bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200"
                >
                    Reset
                </a>
            </div>
        </form>

        <p class="mt-3 text-xs text-gray-500">
            Filters apply automatically. Press Enter while searching to filter immediately.
        </p>
    </div>

    <div id="print-area" class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4">
            <div>
                <h2 class="font-semibold text-gray-900">Assets</h2>
                <p class="mt-1 text-sm text-gray-500">{{ number_format($devices->total()) }} result(s)</p>
            </div>

            <button
                type="button"
                onclick="window.print()"
                class="no-print rounded-xl bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-black"
            >
                Print
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-4 py-3">Type</th>
                        <th class="px-4 py-3">Property #</th>
                        <th class="px-4 py-3">Serial #</th>
                        <th class="px-4 py-3">Brand / Model</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Condition</th>
                        <th class="px-4 py-3">Unit Price</th>
                        <th class="px-4 py-3">College</th>
                        <th class="px-4 py-3">Office</th>
                        <th class="px-4 py-3">Assigned To</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">
                    @forelse($devices as $device)
                        @php
                            $assignment = $device->currentAssignment;
                            $staff = $assignment?->staff;
                            $office = $staff?->office;
                            $college = $office?->college;
                            $staffName = $staff ? trim(($staff->last_name ?? '') . ', ' . ($staff->first_name ?? '')) : '-';
                        @endphp

                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-900">{{ $device->type?->name ?? '-' }}</td>

                            <td class="px-4 py-3 font-medium text-blue-700">
                                <a href="{{ route('admin.devices.show', $device) }}" class="hover:underline">
                                    {{ $device->property_number }}
                                </a>
                            </td>

                            <td class="px-4 py-3 text-gray-700">{{ $device->serial_number ?: '-' }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ trim(($device->brand ?? '') . ' ' . ($device->model ?? '')) ?: '-' }}</td>
                            <td class="px-4 py-3 text-gray-700 capitalize">{{ $device->status ?: '-' }}</td>
                            <td class="px-4 py-3 text-gray-700 capitalize">{{ $device->condition ?: '-' }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $device->unit_price ? number_format((float) $device->unit_price, 2) : '-' }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $college?->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $office?->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $staffName ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-8 text-center text-gray-500">
                                No assets found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="no-print border-t border-gray-200 px-5 py-4">
            {{ $devices->links() }}
        </div>
    </div>
</div>

<script>
    (function () {
        const form = document.getElementById('asset-filter-form');
        const search = document.getElementById('asset-search');
        const typeFilter = document.getElementById('asset-type-filter');
        const collegeFilter = document.getElementById('asset-college-filter');
        const officeFilter = document.getElementById('asset-office-filter');

        if (!form) return;

        let timer = null;

        function submitNow() {
            form.requestSubmit ? form.requestSubmit() : form.submit();
        }

        function submitDebounced() {
            clearTimeout(timer);
            timer = setTimeout(submitNow, 500);
        }

        function filterOfficeOptions() {
            if (!collegeFilter || !officeFilter) return;

            const selectedCollegeId = collegeFilter.value;

            Array.from(officeFilter.options).forEach((option) => {
                if (!option.value) {
                    option.hidden = false;
                    return;
                }

                const optionCollegeId = option.getAttribute('data-college-id');
                option.hidden = selectedCollegeId && optionCollegeId !== selectedCollegeId;
            });

            const selectedOption = officeFilter.options[officeFilter.selectedIndex];

            if (selectedOption && selectedOption.hidden) {
                officeFilter.value = '';
            }
        }

        filterOfficeOptions();

        if (search) {
            search.addEventListener('input', submitDebounced);

            search.addEventListener('keydown', function (event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    submitNow();
                }
            });
        }

        [typeFilter, collegeFilter, officeFilter].forEach((select) => {
            if (!select) return;

            select.addEventListener('change', function () {
                if (select === collegeFilter) {
                    filterOfficeOptions();
                }

                submitNow();
            });
        });
    })();
</script>

<style>
    @media print {
        body * {
            visibility: hidden !important;
        }

        #print-area,
        #print-area * {
            visibility: visible !important;
        }

        #print-area {
            position: absolute !important;
            left: 0 !important;
            top: 0 !important;
            width: 100% !important;
            background: #ffffff !important;
        }

        .no-print {
            display: none !important;
        }

        @page {
            size: A4 landscape;
            margin: 10mm;
        }
    }
</style>
@endsection
