@extends('admin.layouts.app')

@section('title', 'Maintenance Checklist')
@section('page_title', 'Maintenance Checklist')

@section('content')
@php
    $assignment = $device->currentAssignment;
    $staff = $assignment?->staff;
    $office = $staff?->office;
    $college = $office?->college;
@endphp

<div class="mx-auto max-w-6xl space-y-5">
    <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Preventive Maintenance Checklist</h1>
                <p class="mt-1 text-sm text-gray-500">
                    Select OK or Not OK for each hardware item. For software, choose ✓ or -.
                </p>
            </div>

            <a
                href="{{ route('admin.devices.show', $device) }}"
                class="inline-flex items-center justify-center rounded-xl bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200"
            >
                Back to Device
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.devices.checklist.generate', $device) }}" target="_self" class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
        @csrf

        @if($errors->any())
            <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <div class="font-semibold">Please check the checklist form.</div>
                <ul class="mt-1 list-inside list-disc">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <div>
                <label class="text-sm font-medium text-gray-700">Date Checked</label>
                <input
                    type="date"
                    name="maintenance_date"
                    value="{{ old('maintenance_date', $defaultDate) }}"
                    max="{{ now()->format('Y-m-d') }}"
                    class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                    required
                >
            </div>

            <div>
                <label class="text-sm font-medium text-gray-700">Office / Unit</label>
                <input
                    type="text"
                    value="{{ $office?->name ?? 'Unassigned' }}"
                    class="mt-1 w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-gray-700"
                    readonly
                >
            </div>

            <div>
                <label class="text-sm font-medium text-gray-700">College</label>
                <input
                    type="text"
                    value="{{ $college?->name ?? '-' }}"
                    class="mt-1 w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-gray-700"
                    readonly
                >
            </div>
        </div>

        <div class="mt-5 rounded-xl border border-gray-200 bg-gray-50 p-4">
            <div class="grid grid-cols-1 gap-3 text-sm md:grid-cols-4">
                <div>
                    <div class="text-gray-500">Device Type</div>
                    <div class="font-semibold text-gray-900">{{ $device->type?->name ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-gray-500">Property Number</div>
                    <div class="font-semibold text-gray-900">{{ $device->property_number }}</div>
                </div>
                <div>
                    <div class="text-gray-500">Serial Number</div>
                    <div class="font-semibold text-gray-900">{{ $device->serial_number ?: '-' }}</div>
                </div>
                <div>
                    <div class="text-gray-500">Checked By</div>
                    <div class="font-semibold text-gray-900">{{ auth()->user()->name ?? 'Current user' }}</div>
                </div>
            </div>
        </div>

        <div class="mt-5 overflow-x-auto rounded-xl border border-gray-200">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-left text-gray-700">
                    <tr>
                        <th class="w-1/3 px-4 py-3 font-semibold">Checklist Item</th>
                        <th class="px-4 py-3 font-semibold">Selection</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($checklistItems as $key => $label)
                        <tr>
                            <td class="px-4 py-3 text-gray-800">{{ $label }}</td>
                            <td class="px-4 py-3">
                                <select name="{{ $key }}" class="w-full rounded-lg border border-gray-300 px-3 py-2">
                                    <option value="">-- Select --</option>
                                    <option value="OK" @selected(old($key) === 'OK')>OK</option>
                                    <option value="Not OK" @selected(old($key) === 'Not OK')>Not OK</option>
                                </select>
                            </td>
                        </tr>
                    @endforeach

                    @foreach($softwareItems as $key => $label)
                        <tr>
                            <td class="px-4 py-3 text-gray-800">{{ $label }}</td>
                            <td class="px-4 py-3">
                                <select name="{{ $key }}" class="w-full rounded-lg border border-gray-300 px-3 py-2">
                                    <option value="dash" @selected(old($key, 'dash') === 'dash')>-</option>
                                    <option value="check" @selected(old($key) === 'check')>✓</option>
                                </select>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
                <label class="text-sm font-medium text-gray-700">Remarks</label>
                <textarea
                    name="remarks"
                    rows="4"
                    maxlength="1000"
                    class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                    placeholder="Optional remarks"
                >{{ old('remarks') }}</textarea>
            </div>

            <div>
                <label class="text-sm font-medium text-gray-700">Corrective Action</label>
                <textarea
                    name="corrective_action"
                    rows="4"
                    maxlength="1000"
                    class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                    placeholder="Optional corrective action"
                >{{ old('corrective_action') }}</textarea>
            </div>
        </div>

        <div class="mt-6 flex flex-col gap-2 sm:flex-row sm:justify-end">
            <a
                href="{{ route('admin.devices.index') }}"
                class="inline-flex items-center justify-center rounded-xl bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200"
            >
                Cancel
            </a>

            <button
                type="submit"
                class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
            >
                Generate PDF Checklist
            </button>
        </div>
    </form>
</div>
@endsection
