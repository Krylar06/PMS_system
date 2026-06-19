@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content')
<div
    x-data="{
        addDeviceOpen: {{ $errors->any() ? 'true' : 'false' }},
        selectedDeviceTypeId: '{{ old('device_type_id', $types->first()?->id) }}',

        deviceTypes: @js(
            $types->map(function ($type) {
                return [
                    'id' => (string) $type->id,
                    'name' => $type->name,
                ];
            })->values()
        ),

        selectedDeviceTypeName() {
            let selected = this.deviceTypes.find(type => type.id === String(this.selectedDeviceTypeId));
            return selected ? selected.name.toLowerCase() : '';
        },

        isComputerType() {
            return ['desktop', 'laptop'].includes(this.selectedDeviceTypeName());
        }
    }"
    class="space-y-6"
>
    {{-- Page Header --}}
    <div class="flex flex-col gap-1">
        <h1 class="text-2xl font-semibold text-gray-900">
            Dashboard
        </h1>

        <p class="text-sm text-gray-500">
            Overview of device inventory, issuing activity, and recent maintenance records.
        </p>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <div class="font-semibold">Please check the form.</div>
            <ul class="mt-1 list-inside list-disc">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-5">
        <a
            href="{{ route('admin.devices.index') }}"
            class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"
        >
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">
                        Total Devices
                    </p>

                    <div class="mt-3 text-3xl font-semibold text-gray-900">
                        {{ number_format($totalDevices ?? 0) }}
                    </div>
                </div>

                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>

            <p class="mt-4 text-sm text-gray-500">
                All registered devices.
            </p>
        </a>

        <a
            href="{{ route('admin.devices.index') }}"
            class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"
        >
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">
                        Available Devices
                    </p>

                    <div class="mt-3 text-3xl font-semibold text-gray-900">
                        {{ number_format($availableDevices ?? 0) }}
                    </div>
                </div>

                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>

            <p class="mt-4 text-sm text-gray-500">
                Ready to be issued.
            </p>
        </a>

        <a
            href="{{ route('admin.devices.index') }}"
            class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"
        >
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">
                        Issued Devices
                    </p>

                    <div class="mt-3 text-3xl font-semibold text-gray-900">
                        {{ number_format($issuedDevices ?? 0) }}
                    </div>
                </div>

                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7h8M8 11h8M8 15h5M5 4h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2z" />
                    </svg>
                </div>
            </div>

            <p class="mt-4 text-sm text-gray-500">
                Assigned to staff.
            </p>
        </a>

        <a
            href="{{ route('admin.devices.index', ['condition' => 'serviceable']) }}"
            class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-green-200 hover:shadow-md"
        >
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">
                        Serviceable
                    </p>

                    <div class="mt-3 text-3xl font-semibold text-gray-900">
                        {{ number_format($serviceableDevices ?? 0) }}
                    </div>
                </div>

                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-green-50 text-green-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>

            <p class="mt-4 text-sm text-gray-500">
                Working condition.
            </p>
        </a>

        <a
            href="{{ route('admin.devices.index', ['condition' => 'unserviceable']) }}"
            class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-red-200 hover:shadow-md"
        >
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">
                        Unserviceable
                    </p>

                    <div class="mt-3 text-3xl font-semibold text-gray-900">
                        {{ number_format($unserviceableDevices ?? 0) }}
                    </div>
                </div>

                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-red-50 text-red-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 9v3.75m0 3.75h.008M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                    </svg>
                </div>
            </div>

            <p class="mt-4 text-sm text-gray-500">
                Needs checking.
            </p>
        </a>
    </div>

    {{-- Quick Actions --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
        <div class="mb-4">
            <h2 class="text-base font-semibold text-gray-900">
                Quick Actions
            </h2>

            <p class="mt-1 text-sm text-gray-500">
                Common tasks you may need to access quickly.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <button
                type="button"
                @click="addDeviceOpen = true"
                class="flex items-center justify-between rounded-xl border border-gray-200 px-4 py-3 text-sm font-medium text-gray-700 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700"
            >
                <span>Add Device</span>
                <span class="text-lg">+</span>
            </button>

            <a
                href="{{ route('admin.devices.index') }}"
                class="flex items-center justify-between rounded-xl border border-gray-200 px-4 py-3 text-sm font-medium text-gray-700 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700"
            >
                <span>View Devices</span>
                <span>→</span>
            </a>

            <a
                href="{{ route('admin.scanner') }}"
                class="flex items-center justify-between rounded-xl border border-gray-200 px-4 py-3 text-sm font-medium text-gray-700 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700"
            >
                <span>Scan QR Code</span>
                <span>→</span>
            </a>

            <a
                href="{{ route('admin.reports.preventiveMaintenance.export') }}"
                class="flex items-center justify-between rounded-xl border border-gray-200 px-4 py-3 text-sm font-medium text-gray-700 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700"
            >
                <span>Export Report</span>
                <span>↓</span>
            </a>
        </div>
    </div>

    {{-- Recent Tables --}}
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
        {{-- Recent Issued Devices --}}
        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4">
                <div>
                    <h2 class="text-base font-semibold text-gray-900">
                        Recent Issued Devices
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Latest devices assigned to staff.
                    </p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-5 py-3 font-semibold">Device</th>
                            <th class="px-5 py-3 font-semibold">Issued To</th>
                            <th class="px-5 py-3 font-semibold">Date</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200">
                        @forelse($recentIssuedDevices as $assignment)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-4">
                                    @if($assignment->device)
                                        <a
                                            href="{{ route('admin.devices.show', $assignment->device) }}"
                                            class="font-medium text-blue-600 hover:underline"
                                        >
                                            {{ $assignment->device->property_number }}
                                        </a>

                                        <div class="mt-1 text-xs text-gray-500">
                                            {{ $assignment->device->type?->name ?? 'Device' }}

                                            @if($assignment->device->serial_number)
                                                • SN: {{ $assignment->device->serial_number }}
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-gray-400">Device deleted</span>
                                    @endif
                                </td>

                                <td class="px-5 py-4 text-gray-700">
                                    @if($assignment->staff)
                                        <div class="font-medium text-gray-900">
                                            {{ $assignment->staff->last_name }}, {{ $assignment->staff->first_name }}
                                        </div>

                                        <div class="mt-1 text-xs text-gray-500">
                                            {{ $assignment->staff->office?->name ?? 'No office' }}
                                        </div>
                                    @else
                                        <span class="text-gray-400">Staff deleted</span>
                                    @endif
                                </td>

                                <td class="px-5 py-4 text-gray-700">
                                    {{ $assignment->issued_at ? $assignment->issued_at->format('M d, Y') : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-5 py-8 text-center text-gray-500">
                                    No issued devices yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Recent Maintenance Records --}}
        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4">
                <div>
                    <h2 class="text-base font-semibold text-gray-900">
                        Recent Maintenance Records
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Latest checked or maintained devices.
                    </p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-5 py-3 font-semibold">Device</th>
                            <th class="px-5 py-3 font-semibold">Date</th>
                            <th class="px-5 py-3 font-semibold">Remarks</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200">
                        @forelse($recentMaintenanceRecords as $record)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-4">
                                    @if($record->device)
                                        <a
                                            href="{{ route('admin.devices.show', $record->device) }}"
                                            class="font-medium text-blue-600 hover:underline"
                                        >
                                            {{ $record->device->property_number }}
                                        </a>

                                        <div class="mt-1 text-xs text-gray-500">
                                            {{ $record->device->type?->name ?? 'Device' }}

                                            @if($record->device->serial_number)
                                                • SN: {{ $record->device->serial_number }}
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-gray-400">Device deleted</span>
                                    @endif
                                </td>

                                <td class="px-5 py-4 text-gray-700">
                                    {{ $record->maintenance_date ? $record->maintenance_date->format('M d, Y') : '-' }}
                                </td>

                                <td class="px-5 py-4 text-gray-700">
                                    <div class="max-w-xs truncate">
                                        {{ $record->remarks ?: '-' }}
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-5 py-8 text-center text-gray-500">
                                    No maintenance records yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Add Device Modal --}}
    <div
        x-show="addDeviceOpen"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4"
    >
        <div
            @click.away="addDeviceOpen = false"
            class="w-full max-w-4xl overflow-hidden rounded-2xl bg-white shadow-xl"
        >
            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">
                        Add Device
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Register a new device in the inventory.
                    </p>
                </div>

                <button
                    type="button"
                    @click="addDeviceOpen = false"
                    class="rounded-lg px-3 py-1 text-xl text-gray-500 hover:bg-gray-100 hover:text-gray-700"
                >
                    &times;
                </button>
            </div>

            <form method="POST" action="{{ route('admin.devices.store') }}">
                @csrf

                <input type="hidden" name="status" value="available">

                <div class="max-h-[75vh] overflow-y-auto px-6 py-5">
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Device Type
                            </label>

                            <select
                                name="device_type_id"
                                x-model="selectedDeviceTypeId"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                required
                            >
                                @foreach($types as $type)
                                    <option value="{{ $type->id }}">
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>

                            @error('device_type_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Property Number
                            </label>

                            <input
                                type="text"
                                name="property_number"
                                value="{{ old('property_number') }}"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                required
                            >

                            @error('property_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Serial Number
                            </label>

                            <input
                                type="text"
                                name="serial_number"
                                value="{{ old('serial_number') }}"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Enter serial number"
                            >

                            @error('serial_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Brand
                            </label>

                            <input
                                type="text"
                                name="brand"
                                value="{{ old('brand') }}"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Example: ACER, EPSON, SECURE"
                            >

                            @error('brand')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Model
                            </label>

                            <input
                                type="text"
                                name="model"
                                value="{{ old('model') }}"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Example: L3210, 2199"
                            >

                            @error('model')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div x-show="isComputerType()" x-cloak>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                MAC Address
                            </label>

                            <input
                                type="text"
                                name="mac_address"
                                value="{{ old('mac_address') }}"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="00:1A:2B:3C:4D:5E"
                                :disabled="!isComputerType()"
                            >

                            @error('mac_address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div x-show="isComputerType()" x-cloak>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Operating System
                            </label>

                            <input
                                type="text"
                                name="specs[os_version]"
                                value="{{ old('specs.os_version') }}"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Example: Windows 10"
                                :disabled="!isComputerType()"
                            >

                            @error('specs.os_version')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div x-show="isComputerType()" x-cloak>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Memory
                            </label>

                            <input
                                type="text"
                                name="specs[memory]"
                                value="{{ old('specs.memory') }}"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Example: 8GB RAM"
                                :disabled="!isComputerType()"
                            >

                            @error('specs.memory')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div x-show="isComputerType()" x-cloak>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Storage
                            </label>

                            <input
                                type="text"
                                name="specs[storage]"
                                value="{{ old('specs.storage') }}"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Example: 256GB SSD / 1TB HDD"
                                :disabled="!isComputerType()"
                            >

                            @error('specs.storage')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div x-show="isComputerType()" x-cloak>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Form Factor
                            </label>

                            <input
                                type="text"
                                name="specs[form_factor]"
                                value="{{ old('specs.form_factor') }}"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Example: Tower, SFF, Mini PC, All-in-One"
                                :disabled="!isComputerType()"
                            >

                            @error('specs.form_factor')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Unit Price
                            </label>

                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                name="unit_price"
                                value="{{ old('unit_price') }}"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >

                            @error('unit_price')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Date Acquired
                            </label>

                            <input
                                type="date"
                                name="date_acquired"
                                value="{{ old('date_acquired') }}"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >

                            @error('date_acquired')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Condition
                            </label>

                            <select
                                name="condition"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                                <option value="serviceable" @selected(old('condition', 'serviceable') === 'serviceable')>
                                    Serviceable
                                </option>

                                <option value="unserviceable" @selected(old('condition') === 'unserviceable')>
                                    Unserviceable
                                </option>
                            </select>

                            @error('condition')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Last Maintenance Date
                            </label>

                            <input
                                type="date"
                                name="last_maintenance_date"
                                value="{{ old('last_maintenance_date') }}"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >

                            @error('last_maintenance_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-5">
                        <label class="mb-1 block text-sm font-medium text-gray-700">
                            Maintenance Remarks
                        </label>

                        <textarea
                            name="maintenance_remarks"
                            rows="3"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Example: Initial check, cleaned, inspected"
                        >{{ old('maintenance_remarks') }}</textarea>

                        @error('maintenance_remarks')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-5">
                        <label class="mb-1 block text-sm font-medium text-gray-700">
                            Notes
                        </label>

                        <textarea
                            name="notes"
                            rows="4"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >{{ old('notes') }}</textarea>

                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end gap-2 border-t border-gray-200 px-6 py-4">
                    <button
                        type="button"
                        @click="addDeviceOpen = false"
                        class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200"
                    >
                        Cancel
                    </button>

                    <button
                        type="submit"
                        class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
                    >
                        Save Device
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
