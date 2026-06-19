@extends('admin.layouts.app')

@section('title', 'Device Details')
@section('page_title', 'Device Details')

@section('content')
@php
    $deviceTypeName = strtolower($device->type?->name ?? '');
    $isComputerType = in_array($deviceTypeName, ['desktop', 'laptop']);
    $deviceUrl = route('admin.devices.show', $device);
@endphp

<div
    x-data="{
        editOpen: false,
        selectedTypeId: @json(old('device_type_id', $device->device_type_id)),

        typeNames: @json($types->pluck('name', 'id')),
        getTypeName(typeId) {
            return (this.typeNames[typeId] || '').toLowerCase();
        },

        isComputerType(typeId = null) {
            let selected = typeId ?? this.selectedTypeId;
            let name = this.getTypeName(selected);

            return name === 'desktop' || name === 'laptop';
        }
    }"
    class="grid grid-cols-1 gap-6 lg:grid-cols-3"
>
    <div class="lg:col-span-2">
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">
                        {{ $device->property_number }}
                    </h1>

                    <p class="mt-1 text-gray-500">
                        {{ $device->type?->name ?? 'Device' }}
                    </p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <a
                        href="{{ route('admin.devices.index') }}"
                        class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200"
                    >
                        Back
                    </a>

                    <a
                        href="{{ route('admin.devices.history', $device) }}"
                        class="rounded-lg bg-purple-600 px-4 py-2 text-sm font-medium text-white hover:bg-purple-700"
                    >
                        History
                    </a>

                    <form
                        method="POST"
                        action="{{ route('admin.devices.markChecked', $device) }}"
                        onsubmit="return confirm('Mark this device as checked/maintained today?')"
                    >
                        @csrf
                        @method('PATCH')

                        <button
                            type="submit"
                            class="rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700"
                        >
                            Mark as Checked
                        </button>
                    </form>

                    <button
                        type="button"
                        x-on:click="editOpen = true"
                        class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
                    >
                        Edit
                    </button>

                    <form
                        method="POST"
                        action="{{ route('admin.devices.destroy', $device) }}"
                        onsubmit="return confirm('Delete this device?')"
                    >
                        @csrf
                        @method('DELETE')

                        <button
                            type="submit"
                            class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700"
                        >
                            Delete
                        </button>
                    </form>
                </div>
            </div>

            <div class="mt-8 grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <div class="text-sm text-gray-500">Device Type</div>
                    <div class="font-medium text-gray-900">
                        {{ $device->type?->name ?? '-' }}
                    </div>
                </div>

                <div>
                    <div class="text-sm text-gray-500">Property Number</div>
                    <div class="font-medium text-gray-900">
                        {{ $device->property_number }}
                    </div>
                </div>

                <div>
                    <div class="text-sm text-gray-500">Serial Number</div>
                    <div class="font-medium text-gray-900">
                        {{ $device->serial_number ?: '-' }}
                    </div>
                </div>

                <div>
                    <div class="text-sm text-gray-500">Brand</div>
                    <div class="font-medium text-gray-900">
                        {{ $device->brand ?: '-' }}
                    </div>
                </div>

                <div>
                    <div class="text-sm text-gray-500">Model</div>
                    <div class="font-medium text-gray-900">
                        {{ $device->model ?: '-' }}
                    </div>
                </div>

                @if($isComputerType)
                    <div>
                        <div class="text-sm text-gray-500">MAC Address</div>
                        <div class="font-medium text-gray-900">
                            {{ $device->mac_address ?: '-' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-sm text-gray-500">Operating System</div>
                            <div class="font-medium text-gray-900">
                                {{ data_get($device->specs, 'os_version', '-') ?: '-' }}
                            </div>
                    </div>

                    <div>
                        <div class="text-sm text-gray-500">Memory</div>
                        <div class="font-medium text-gray-900">
                            {{ data_get($device->specs, 'memory', '-') ?: '-' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-sm text-gray-500">Storage</div>
                        <div class="font-medium text-gray-900">
                            {{ data_get($device->specs, 'storage', '-') ?: '-' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-sm text-gray-500">Form Factor</div>
                        <div class="font-medium text-gray-900">
                            {{ data_get($device->specs, 'form_factor', '-') ?: '-' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-sm text-gray-500">Microsoft Office</div>
                        <div class="font-medium text-gray-900">
                            {{ data_get($device->specs, 'office_version', '-') ?: '-' }}
                        </div>
                    </div>
                @endif

                <div>
                    <div class="text-sm text-gray-500">Unit Price</div>
                    <div class="font-medium text-gray-900">
                        {{ $device->unit_price ? number_format($device->unit_price, 2) : '-' }}
                    </div>
                </div>

                <div>
                    <div class="text-sm text-gray-500">Date Acquired</div>
                    <div class="font-medium text-gray-900">
                        {{ $device->date_acquired ? $device->date_acquired->format('Y-m-d') : '-' }}
                    </div>
                </div>

                <div>
                    <div class="text-sm text-gray-500">Condition</div>
                    <div class="font-medium text-gray-900 capitalize">
                        {{ $device->condition ?? 'serviceable' }}
                    </div>
                </div>

                <div>
                    <div class="text-sm text-gray-500">Last Maintenance</div>
                    <div class="font-medium text-gray-900">
                        {{ $device->last_maintenance_date ? $device->last_maintenance_date->format('M d, Y') : 'Not yet checked' }}
                    </div>
                </div>
            </div>

            @if($device->maintenance_remarks)
                <div class="mt-8 border-t border-gray-200 pt-6">
                    <h2 class="font-semibold text-gray-900">
                        Maintenance Remarks
                    </h2>

                    <p class="mt-3 text-gray-700">
                        {{ $device->maintenance_remarks }}
                    </p>
                </div>
            @endif

            <div class="mt-8 border-t border-gray-200 pt-6">
                <h2 class="font-semibold text-gray-900">
                    Current Assignment
                </h2>

                @if($device->currentAssignment && $device->currentAssignment->staff)
                    <div class="mt-3 rounded-xl border border-gray-200 bg-gray-50 p-4">
                        <div class="font-medium text-gray-900">
                            {{ $device->currentAssignment->staff->last_name }},
                            {{ $device->currentAssignment->staff->first_name }}
                        </div>

                        <div class="mt-1 text-sm text-gray-500">
                            {{ $device->currentAssignment->staff->office?->name ?? 'No office' }}

                            @if($device->currentAssignment->staff->office?->college)
                                /
                                {{ $device->currentAssignment->staff->office->college->name }}
                            @endif
                        </div>

                        <div class="mt-1 text-sm text-gray-500">
                            Issued:
                            {{ $device->currentAssignment->issued_at ? $device->currentAssignment->issued_at->format('M d, Y h:i A') : '-' }}
                        </div>
                    </div>
                @else
                    <p class="mt-3 text-gray-700">
                        This device is not currently issued.
                    </p>
                @endif
            </div>

            @if($device->notes)
                <div class="mt-8 border-t border-gray-200 pt-6">
                    <h2 class="font-semibold text-gray-900">
                        Notes
                    </h2>

                    <p class="mt-3 text-gray-700">
                        {{ $device->notes }}
                    </p>
                </div>
            @endif
        </div>
    </div>

    {{-- EDIT MODAL --}}
    <x-modal show="editOpen" title="Edit Device">
        <form method="POST" action="{{ route('admin.devices.update', $device) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <input type="hidden" name="status" value="{{ $device->status ?? 'available' }}">

            <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                <div>
                    <label class="text-sm font-medium">Device Type</label>
                    <select
                        name="device_type_id"
                        x-model="selectedTypeId"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        required
                    >
                        @foreach($types as $type)
                            <option value="{{ $type->id }}">
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-sm font-medium">Property Number</label>
                    <input
                        name="property_number"
                        value="{{ old('property_number', $device->property_number) }}"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        required
                    >
                </div>

                <div>
                    <label class="text-sm font-medium">Serial Number</label>
                    <input
                        name="serial_number"
                        value="{{ old('serial_number', $device->serial_number) }}"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        placeholder="Enter serial number"
                    >
                </div>

                <div>
                    <label class="text-sm font-medium">Brand</label>
                    <input
                        name="brand"
                        value="{{ old('brand', $device->brand) }}"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                    >
                </div>

                <div>
                    <label class="text-sm font-medium">Model</label>
                    <input
                        name="model"
                        value="{{ old('model', $device->model) }}"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                    >
                </div>

                <div x-show="isComputerType()" x-cloak>
                    <label class="text-sm font-medium">MAC Address</label>
                    <input
                        name="mac_address"
                        value="{{ old('mac_address', $device->mac_address) }}"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        :disabled="!isComputerType()"
                    >
                </div>

                <div x-show="isComputerType()" x-cloak>
                    <label class="text-sm font-medium">Operating System</label>
                    <input
                        name="specs[os_version]"
                        value="{{ old('specs.os_version', data_get($device->specs, 'os_version')) }}"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        :disabled="!isComputerType()"
                    >
                </div>

                <div x-show="isComputerType()" x-cloak>
                    <label class="text-sm font-medium">Memory</label>
                    <input
                        name="specs[memory]"
                        value="{{ old('specs.memory', data_get($device->specs, 'memory')) }}"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        :disabled="!isComputerType()"
                    >
                </div>

                <div x-show="isComputerType()" x-cloak>
                    <label class="text-sm font-medium">Microsoft Office Version</label>
                    <input
                        name="specs[office_version]"
                        value="{{ old('specs.office_version', data_get($device->specs, 'office_version')) }}"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        :disabled="!isComputerType()"
                    >
                </div>

                <div x-show="isComputerType()" x-cloak>
                    <label class="text-sm font-medium">Storage</label>
                    <input
                        name="specs[storage]"
                        value="{{ old('specs.storage', data_get($device->specs, 'storage')) }}"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        :disabled="!isComputerType()"
                    >
                </div>

                <div x-show="isComputerType()" x-cloak>
                    <label class="text-sm font-medium">Form Factor</label>
                    <input
                        name="specs[form_factor]"
                        value="{{ old('specs.form_factor', data_get($device->specs, 'form_factor')) }}"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        :disabled="!isComputerType()"
                    >
                </div>

                <div>
                    <label class="text-sm font-medium">Unit Price</label>
                    <input
                        name="unit_price"
                        type="number"
                        step="0.01"
                        value="{{ old('unit_price', $device->unit_price) }}"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                    >
                </div>

                <div>
                    <label class="text-sm font-medium">Date Acquired</label>
                    <input
                        name="date_acquired"
                        type="date"
                        value="{{ old('date_acquired', $device->date_acquired ? $device->date_acquired->format('Y-m-d') : '') }}"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                    >
                </div>

                <div>
                    <label class="text-sm font-medium">Condition</label>
                    <select
                        name="condition"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                    >
                        <option value="serviceable" @selected(old('condition', $device->condition ?? 'serviceable') === 'serviceable')>
                            Serviceable
                        </option>

                        <option value="unserviceable" @selected(old('condition', $device->condition) === 'unserviceable')>
                            Unserviceable
                        </option>
                    </select>
                </div>

                <div>
                    <label class="text-sm font-medium">Last Maintenance Date</label>
                    <input
                        name="last_maintenance_date"
                        type="date"
                        value="{{ old('last_maintenance_date', $device->last_maintenance_date ? $device->last_maintenance_date->format('Y-m-d') : '') }}"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                    >
                </div>
            </div>

            <div>
                <label class="text-sm font-medium">Maintenance Remarks</label>
                <textarea
                    name="maintenance_remarks"
                    rows="3"
                    class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                >{{ old('maintenance_remarks', $device->maintenance_remarks) }}</textarea>
            </div>

            <div>
                <label class="text-sm font-medium">Notes</label>
                <textarea
                    name="notes"
                    rows="3"
                    class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                >{{ old('notes', $device->notes) }}</textarea>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button
                    type="button"
                    x-on:click="editOpen = false"
                    class="rounded-lg bg-gray-100 px-4 py-2 text-gray-700 hover:bg-gray-200"
                >
                    Cancel
                </button>

                <button
                    type="submit"
                    class="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700"
                >
                    Save Changes
                </button>
            </div>
        </form>
    </x-modal>
</div>
@endsection
