@extends('admin.layouts.app')

@section('title', 'Maintenance Checklist')
@section('page_title', 'Maintenance Checklist')
@section('breadcrumbs')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600">Dashboard</a>
    <span>/</span>
    <a href="{{ route('admin.devices.index') }}" class="hover:text-blue-600">Equipment Manager</a>
    <span>/</span>
    <a href="{{ route('admin.devices.show', $device) }}" class="hover:text-blue-600">Device Details</a>
    <span>/</span>
    <span class="font-medium text-gray-800">Maintenance Checklist</span>
@endsection

@section('content')
@php
    $assignment = $device->currentAssignment;
    $staff = $assignment?->staff;
    $office = $staff?->office;
    $college = $office?->college;
@endphp

<div class="space-y-6">
    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">
                    Preventive Maintenance Checklist
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Choose OK or Not OK for each hardware item. For software, choose ✓ or -.
                </p>
            </div>

            <a
                href="{{ route('admin.devices.show', $device) }}"
                class="inline-flex rounded-xl bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200"
            >
                Back to Device
            </a>
        </div>
    </div>

    <form
        method="POST"
        action="{{ route('admin.devices.checklist.save', $device) }}"
        target="_self"
        class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm"
    >
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

        <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Date Checked</label>
                <input
                    type="date"
                    name="date_checked"
                    value="{{ old('date_checked', $defaultDate ?? now()->toDateString()) }}"
                    max="{{ now()->toDateString() }}"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2"
                >
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Office / Unit</label>
                <input
                    type="text"
                    value="{{ $office?->name ?? 'Unassigned' }}"
                    readonly
                    class="w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2"
                >
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">College</label>
                <input
                    type="text"
                    value="{{ $college?->name ?? '-' }}"
                    readonly
                    class="w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2"
                >
            </div>
        </div>

        <div class="mt-6 rounded-xl border border-gray-200 bg-gray-50 p-5">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                <div>
                    <div class="text-sm text-gray-500">Device Type</div>
                    <div class="font-semibold text-gray-900">{{ $device->type?->name ?? '-' }}</div>
                </div>

                <div>
                    <div class="text-sm text-gray-500">Property Number</div>
                    <div class="font-semibold text-gray-900">{{ $device->property_number }}</div>
                </div>

                <div>
                    <div class="text-sm text-gray-500">Serial Number</div>
                    <div class="font-semibold text-gray-900">{{ $device->serial_number ?: '-' }}</div>
                </div>

                <div>
                    <div class="text-sm text-gray-500">Checked By</div>
                    <div class="font-semibold text-gray-900">{{ auth()->user()->name ?? '-' }}</div>
                </div>
            </div>
        </div>

        <div class="mt-6 overflow-x-auto rounded-xl border border-gray-200">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-left">
                    <tr>
                        <th class="px-4 py-3 font-semibold text-gray-700">Section</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Checklist Item</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700">OK</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700">Not OK</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">
                    @foreach($checklistItems as $key => $item)
                        <tr>
                            <td class="px-4 py-3 text-gray-700">{{ $item['group'] ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-800">{{ $item['label'] ?? '-' }}</td>

                            <td class="px-4 py-3 text-center">
                                <label class="inline-flex cursor-pointer items-center justify-center">
                                    <input
                                        type="radio"
                                        name="hardware[{{ $key }}]"
                                        value="OK"
                                        class="peer sr-only"
                                        @checked(old("hardware.$key") === 'OK')
                                    >
                                    <span class="flex h-8 w-8 items-center justify-center rounded border-2 border-gray-400 text-lg font-bold text-transparent peer-checked:border-green-600 peer-checked:bg-green-50 peer-checked:text-green-700">
                                        ✓
                                    </span>
                                </label>
                            </td>

                            <td class="px-4 py-3 text-center">
                                <label class="inline-flex cursor-pointer items-center justify-center">
                                    <input
                                        type="radio"
                                        name="hardware[{{ $key }}]"
                                        value="Not OK"
                                        class="peer sr-only"
                                        @checked(old("hardware.$key") === 'Not OK')
                                    >
                                    <span class="flex h-8 w-8 items-center justify-center rounded border-2 border-gray-400 text-lg font-bold text-transparent peer-checked:border-red-600 peer-checked:bg-red-50 peer-checked:text-red-700">
                                        ✓
                                    </span>
                                </label>
                            </td>
                        </tr>
                    @endforeach

                    @foreach($softwareItems as $key => $label)
                        <tr>
                            <td class="px-4 py-3 text-gray-700">Software</td>
                            <td class="px-4 py-3 text-gray-800">{{ $label }}</td>

                            <td class="px-4 py-3 text-center">
                                <label class="inline-flex cursor-pointer items-center justify-center">
                                    <input
                                        type="radio"
                                        name="software[{{ $key }}]"
                                        value="check"
                                        class="peer sr-only"
                                        @checked(old("software.$key") === 'check')
                                    >
                                    <span class="flex h-8 w-8 items-center justify-center rounded border-2 border-gray-400 text-lg font-bold text-transparent peer-checked:border-green-600 peer-checked:bg-green-50 peer-checked:text-green-700">
                                        ✓
                                    </span>
                                </label>
                            </td>

                            <td class="px-4 py-3 text-center">
                                <label class="inline-flex cursor-pointer items-center justify-center">
                                    <input
                                        type="radio"
                                        name="software[{{ $key }}]"
                                        value="dash"
                                        class="peer sr-only"
                                        @checked(old("software.$key") === 'dash')
                                    >
                                    <span class="flex h-8 w-8 items-center justify-center rounded border-2 border-gray-400 text-lg font-bold text-transparent peer-checked:border-gray-600 peer-checked:bg-gray-50 peer-checked:text-gray-700">
                                        -
                                    </span>
                                </label>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-5 md:grid-cols-2">
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Remarks</label>
                <textarea
                    name="remarks"
                    rows="4"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2"
                    placeholder="Optional remarks"
                >{{ old('remarks') }}</textarea>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Corrective Action</label>
                <textarea
                    name="corrective_action"
                    rows="4"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2"
                    placeholder="Optional corrective action"
                >{{ old('corrective_action') }}</textarea>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-2">
            <a
                href="{{ route('admin.devices.show', $device) }}"
                class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200"
            >
                Cancel
            </a>

            <button
                type="submit"
                class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
            >
                Save Checklist
            </button>
        </div>
    </form>
</div>
@endsection
