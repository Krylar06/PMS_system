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
        <h1 class="text-2xl font-semibold text-gray-900">Dashboard</h1>
        <p class="text-sm text-gray-500">Overview of device inventory, issuing activity, and recent maintenance records.</p>
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
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <a href="{{ route('admin.devices.index') }}" class="rounded-2xl border-l-4 border-blue-500 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            <p class="text-xs font-semibold uppercase tracking-widest text-blue-500">Total Devices</p>
            <div class="mt-2 text-4xl font-bold text-gray-900">{{ number_format($totalDevices ?? 0) }}</div>
            <p class="mt-1 text-sm text-gray-400">All registered devices</p>
        </a>
        <a href="{{ route('admin.devices.index') }}" class="rounded-2xl border-l-4 border-emerald-500 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            <p class="text-xs font-semibold uppercase tracking-widest text-emerald-500">Available</p>
            <div class="mt-2 text-4xl font-bold text-gray-900">{{ number_format($availableDevices ?? 0) }}</div>
            <p class="mt-1 text-sm text-gray-400">Ready to be issued</p>
        </a>
        <a href="{{ route('admin.devices.index') }}" class="rounded-2xl border-l-4 border-indigo-500 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            <p class="text-xs font-semibold uppercase tracking-widest text-indigo-500">Issued</p>
            <div class="mt-2 text-4xl font-bold text-gray-900">{{ number_format($issuedDevices ?? 0) }}</div>
            <p class="mt-1 text-sm text-gray-400">Assigned to staff</p>
        </a>
        <a href="{{ route('admin.devices.index', ['condition' => 'serviceable']) }}" class="rounded-2xl border-l-4 border-green-500 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            <p class="text-xs font-semibold uppercase tracking-widest text-green-500">Serviceable</p>
            <div class="mt-2 text-4xl font-bold text-gray-900">{{ number_format($serviceableDevices ?? 0) }}</div>
            <p class="mt-1 text-sm text-gray-400">Working condition</p>
        </a>
        <a href="{{ route('admin.devices.index', ['condition' => 'unserviceable']) }}" class="rounded-2xl border-l-4 border-red-500 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            <p class="text-xs font-semibold uppercase tracking-widest text-red-500">Unserviceable</p>
            <div class="mt-2 text-4xl font-bold text-gray-900">{{ number_format($unserviceableDevices ?? 0) }}</div>
            <p class="mt-1 text-sm text-gray-400">Needs checking</p>
        </a>
    </div>

    {{-- Quick Actions --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
        <div class="mb-4">
            <h2 class="text-base font-semibold text-gray-900">Quick Actions</h2>
            <p class="mt-1 text-sm text-gray-500">Common tasks you may need to access quickly.</p>
        </div>
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <button type="button" @click="addDeviceOpen = true" class="flex items-center justify-between rounded-xl border border-gray-200 px-4 py-3 text-sm font-medium text-gray-700 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700">
                <span>Add Device</span><span class="text-lg">+</span>
            </button>
            <a href="{{ route('admin.devices.index') }}" class="flex items-center justify-between rounded-xl border border-gray-200 px-4 py-3 text-sm font-medium text-gray-700 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700">
                <span>View Devices</span><span>→</span>
            </a>
            <a href="{{ route('admin.scanner') }}" class="flex items-center justify-between rounded-xl border border-gray-200 px-4 py-3 text-sm font-medium text-gray-700 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700">
                <span>Scan QR Code</span><span>→</span>
            </a>
            <a href="{{ route('admin.reports.preventiveMaintenance.export') }}" class="flex items-center justify-between rounded-xl border border-gray-200 px-4 py-3 text-sm font-medium text-gray-700 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700">
                <span>Export Report</span><span>↓</span>
            </a>
        </div>
    </div>

    {{-- Charts 2x2 --}}
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">

        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <h2 class="text-base font-semibold text-gray-900">Devices by Status</h2>
            <p class="mt-1 mb-4 text-sm text-gray-500">Current status breakdown.</p>
            <div style="position:relative; height:250px;">
                <canvas id="statusChart"></canvas>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <h2 class="text-base font-semibold text-gray-900">Devices by Type</h2>
            <p class="mt-1 mb-4 text-sm text-gray-500">Distribution across device categories.</p>
            <div style="position:relative; height:250px;">
                <canvas id="typeChart"></canvas>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <h2 class="text-base font-semibold text-gray-900">Devices by Office</h2>
            <p class="mt-1 mb-4 text-sm text-gray-500">Issued devices per office.</p>
            <div style="position:relative; height:250px;">
                <canvas id="officeChart"></canvas>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm flex flex-col justify-center items-center text-center">
            <div class="text-5xl font-bold text-blue-600">{{ number_format($totalDevices ?? 0) }}</div>
            <p class="mt-2 text-sm font-medium text-gray-500">Total Devices Registered</p>
            <div class="mt-4 flex gap-4 text-sm text-gray-500">
                <span class="flex items-center gap-1"><span class="inline-block h-2 w-2 rounded-full bg-emerald-500"></span> {{ $availableDevices ?? 0 }} Available</span>
                <span class="flex items-center gap-1"><span class="inline-block h-2 w-2 rounded-full bg-indigo-500"></span> {{ $issuedDevices ?? 0 }} Issued</span>
            </div>
        </div>

    </div>

    {{-- Recent Tables --}}
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
        {{-- Recent Issued Devices --}}
        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4">
                <div>
                    <h2 class="text-base font-semibold text-gray-900">Recent Issued Devices</h2>
                    <p class="mt-1 text-sm text-gray-500">Latest devices assigned to staff.</p>
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
                                        <a href="{{ route('admin.devices.show', $assignment->device) }}" class="font-medium text-blue-600 hover:underline">{{ $assignment->device->property_number }}</a>
                                        <div class="mt-1 text-xs text-gray-500">{{ $assignment->device->type?->name ?? 'Device' }}@if($assignment->device->serial_number) • SN: {{ $assignment->device->serial_number }}@endif</div>
                                    @else
                                        <span class="text-gray-400">Device deleted</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-gray-700">
                                    @if($assignment->staff)
                                        <div class="font-medium text-gray-900">{{ $assignment->staff->last_name }}, {{ $assignment->staff->first_name }}</div>
                                        <div class="mt-1 text-xs text-gray-500">{{ $assignment->staff->office?->name ?? 'No office' }}</div>
                                    @else
                                        <span class="text-gray-400">Staff deleted</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-gray-700">{{ $assignment->issued_at ? $assignment->issued_at->format('M d, Y') : '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-5 py-8 text-center text-gray-500">No issued devices yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Recent Maintenance Records --}}
        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4">
                <div>
                    <h2 class="text-base font-semibold text-gray-900">Recent Maintenance Records</h2>
                    <p class="mt-1 text-sm text-gray-500">Latest checked or maintained devices.</p>
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
                                        <a href="{{ route('admin.devices.show', $record->device) }}" class="font-medium text-blue-600 hover:underline">{{ $record->device->property_number }}</a>
                                        <div class="mt-1 text-xs text-gray-500">{{ $record->device->type?->name ?? 'Device' }}@if($record->device->serial_number) • SN: {{ $record->device->serial_number }}@endif</div>
                                    @else
                                        <span class="text-gray-400">Device deleted</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-gray-700">{{ $record->maintenance_date ? $record->maintenance_date->format('M d, Y') : '-' }}</td>
                                <td class="px-5 py-4 text-gray-700"><div class="max-w-xs truncate">{{ $record->remarks ?: '-' }}</div></td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-5 py-8 text-center text-gray-500">No maintenance records yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Add Device Modal --}}
    <div x-show="addDeviceOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4">
        <div @click.away="addDeviceOpen = false" class="w-full max-w-4xl overflow-hidden rounded-2xl bg-white shadow-xl">
            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Add Device</h2>
                    <p class="mt-1 text-sm text-gray-500">Register a new device in the inventory.</p>
                </div>
                <button type="button" @click="addDeviceOpen = false" class="rounded-lg px-3 py-1 text-xl text-gray-500 hover:bg-gray-100 hover:text-gray-700">&times;</button>
            </div>
            <form method="POST" action="{{ route('admin.devices.store') }}">
                @csrf
                <input type="hidden" name="status" value="available">
                <div class="max-h-[75vh] overflow-y-auto px-6 py-5">
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Device Type</label>
                            <select name="device_type_id" x-model="selectedDeviceTypeId" class="w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                @foreach($types as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                            @error('device_type_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Property Number</label>
                            <input type="text" name="property_number" value="{{ old('property_number') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            @error('property_number')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Serial Number</label>
                            <input type="text" name="serial_number" value="{{ old('serial_number') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Enter serial number">
                            @error('serial_number')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Brand</label>
                            <input type="text" name="brand" value="{{ old('brand') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Example: ACER, EPSON">
                            @error('brand')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Model</label>
                            <input type="text" name="model" value="{{ old('model') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Example: L3210, 2199">
                            @error('model')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div x-show="isComputerType()" x-cloak>
                            <label class="mb-1 block text-sm font-medium text-gray-700">MAC Address</label>
                            <input type="text" name="mac_address" value="{{ old('mac_address') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="00:1A:2B:3C:4D:5E" :disabled="!isComputerType()">
                            @error('mac_address')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div x-show="isComputerType()" x-cloak>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Memory</label>
                            <input type="text" name="specs[memory]" value="{{ old('specs.memory') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Example: 8GB RAM" :disabled="!isComputerType()">
                            @error('specs.memory')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div x-show="isComputerType()" x-cloak>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Storage</label>
                            <input type="text" name="specs[storage]" value="{{ old('specs.storage') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Example: 256GB SSD" :disabled="!isComputerType()">
                            @error('specs.storage')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div x-show="isComputerType()" x-cloak>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Form Factor</label>
                            <input type="text" name="specs[form_factor]" value="{{ old('specs.form_factor') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Example: Tower, SFF" :disabled="!isComputerType()">
                            @error('specs.form_factor')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        {{-- OS Version --}}
                        <div id="dash_os_version_wrapper" style="display:none;">
                            <label class="mb-1 block text-sm font-medium text-gray-700">OS Version</label>
                            <select name="os_version" id="dash_os_version_select" class="w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">-- Select OS --</option>
                                <option value="Windows 7" @selected(old('os_version') === 'Windows 7')>Windows 7</option>
                                <option value="Windows 8" @selected(old('os_version') === 'Windows 8')>Windows 8</option>
                                <option value="Windows 10" @selected(old('os_version') === 'Windows 10')>Windows 10</option>
                                <option value="Windows 11" @selected(old('os_version') === 'Windows 11')>Windows 11</option>
                            </select>
                        </div>

                        {{-- OS License --}}
                        <div id="dash_os_license_wrapper" style="display:none;">
                            <label class="mb-1 block text-sm font-medium text-gray-700">OS License</label>
                            <select name="os_license" class="w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">-- Select License --</option>
                                <option value="Cracked" @selected(old('os_license') === 'Cracked')>Cracked</option>
                                <option value="OEM Licensed" @selected(old('os_license') === 'OEM Licensed')>OEM Licensed</option>
                            </select>
                        </div>

                        {{-- MS Office Version --}}
                        <div id="dash_ms_version_wrapper" style="display:none;">
                            <label class="mb-1 block text-sm font-medium text-gray-700">MS Office Version</label>
                            <select name="ms_office_version" id="dash_ms_version_select" class="w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">-- Select MS Office --</option>
                                <option value="Office 2007" @selected(old('ms_office_version') === 'Office 2007')>Office 2007</option>
                                <option value="Office 2010" @selected(old('ms_office_version') === 'Office 2010')>Office 2010</option>
                                <option value="Office 2013" @selected(old('ms_office_version') === 'Office 2013')>Office 2013</option>
                                <option value="Office 2016" @selected(old('ms_office_version') === 'Office 2016')>Office 2016</option>
                                <option value="Office 2019" @selected(old('ms_office_version') === 'Office 2019')>Office 2019</option>
                                <option value="Office 2021" @selected(old('ms_office_version') === 'Office 2021')>Office 2021</option>
                                <option value="Microsoft 365" @selected(old('ms_office_version') === 'Microsoft 365')>Microsoft 365</option>
                            </select>
                        </div>

                        {{-- MS Office License --}}
                        <div id="dash_ms_license_wrapper" style="display:none;">
                            <label class="mb-1 block text-sm font-medium text-gray-700">MS Office License</label>
                            <select name="ms_office_license" class="w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">-- Select License --</option>
                                <option value="Cracked" @selected(old('ms_office_license') === 'Cracked')>Cracked</option>
                                <option value="OEM Licensed" @selected(old('ms_office_license') === 'OEM Licensed')>OEM Licensed</option>
                            </select>
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Unit Price</label>
                            <input type="number" step="0.01" min="0" name="unit_price" value="{{ old('unit_price') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('unit_price')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Date Acquired</label>
                            <input type="date" name="date_acquired" value="{{ old('date_acquired') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('date_acquired')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Condition</label>
                            <select name="condition" class="w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="serviceable" @selected(old('condition', 'serviceable') === 'serviceable')>Serviceable</option>
                                <option value="unserviceable" @selected(old('condition') === 'unserviceable')>Unserviceable</option>
                            </select>
                            @error('condition')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Last Maintenance Date</label>
                            <input type="date" name="last_maintenance_date" value="{{ old('last_maintenance_date') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('last_maintenance_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div class="mt-5">
                        <label class="mb-1 block text-sm font-medium text-gray-700">Maintenance Remarks</label>
                        <textarea name="maintenance_remarks" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Example: Initial check, cleaned, inspected">{{ old('maintenance_remarks') }}</textarea>
                        @error('maintenance_remarks')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="mt-5">
                        <label class="mb-1 block text-sm font-medium text-gray-700">Notes</label>
                        <textarea name="notes" rows="4" class="w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('notes') }}</textarea>
                        @error('notes')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="flex justify-end gap-2 border-t border-gray-200 px-6 py-4">
                    <button type="button" @click="addDeviceOpen = false" class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200">Cancel</button>
                    <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Save Device</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    new Chart(document.getElementById('statusChart'), {
        type: 'bar',
        data: {
            labels: @json(array_keys($devicesByStatus ?? [])),
            datasets: [{
                label: 'Devices',
                data: @json(array_values($devicesByStatus ?? [])),
                backgroundColor: ['#3b82f6', '#6366f1', '#22c55e', '#ef4444'],
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });

    new Chart(document.getElementById('typeChart'), {
        type: 'doughnut',
        data: {
            labels: @json(($devicesByType ?? collect())->keys()),
            datasets: [{
                data: @json(($devicesByType ?? collect())->values()),
                backgroundColor: ['#3b82f6','#6366f1','#22c55e','#f59e0b','#ef4444','#14b8a6','#ec4899','#8b5cf6'],
                borderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom', labels: { padding: 12, boxWidth: 12 } } }
        }
    });

    new Chart(document.getElementById('officeChart'), {
        type: 'bar',
        data: {
            labels: @json(($devicesByOffice ?? collect())->keys()),
            datasets: [{
                label: 'Issued Devices',
                data: @json(($devicesByOffice ?? collect())->values()),
                backgroundColor: '#6366f1',
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { x: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });
</script>
@endpush

@push('scripts')
<script>
(function () {
    var typeNames = @json($types->pluck('name', 'id'));

    function getTypeName(typeId) {
        return (typeNames[typeId] || '').toLowerCase();
    }

    function isComputer(typeId) {
        var n = getTypeName(typeId);
        return n === 'desktop' || n === 'laptop';
    }

    function show(el) { if (el) el.style.display = ''; }
    function hide(el) { if (el) el.style.display = 'none'; }

    var typeSelect  = document.querySelector('select[name="device_type_id"]');
    var osVerSel    = document.getElementById('dash_os_version_select');
    var msVerSel    = document.getElementById('dash_ms_version_select');
    var osVerWrap   = document.getElementById('dash_os_version_wrapper');
    var osLicWrap   = document.getElementById('dash_os_license_wrapper');
    var msVerWrap   = document.getElementById('dash_ms_version_wrapper');
    var msLicWrap   = document.getElementById('dash_ms_license_wrapper');

    function updateFields() {
        var typeId = typeSelect ? typeSelect.value : '';
        var computer = isComputer(typeId);
        computer ? show(osVerWrap) : hide(osVerWrap);
        computer ? show(msVerWrap) : hide(msVerWrap);
        if (computer && osVerSel && osVerSel.value) { show(osLicWrap); } else { hide(osLicWrap); }
        if (computer && msVerSel && msVerSel.value) { show(msLicWrap); } else { hide(msLicWrap); }
    }

    if (typeSelect) {
        typeSelect.addEventListener('change', updateFields);
        new MutationObserver(updateFields).observe(typeSelect, { attributes: true, childList: true, subtree: true });
    }
    if (osVerSel) {
        osVerSel.addEventListener('change', function () {
            this.value ? show(osLicWrap) : hide(osLicWrap);
        });
    }
    if (msVerSel) {
        msVerSel.addEventListener('change', function () {
            this.value ? show(msLicWrap) : hide(msLicWrap);
        });
    }

    // Re-run when modal opens
    document.addEventListener('click', function () {
        setTimeout(updateFields, 150);
    });

    updateFields();
})();
</script>
@endpush
@endsection
