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
        },

        isDesktopType(typeId = null) {
            let selected = typeId ?? this.selectedTypeId;
            return this.getTypeName(selected) === 'desktop';
        },

        formatUnitPriceValue(value) {
            value = String(value ?? '').replace(/[^0-9.]/g, '');

            let parts = value.split('.');
            let whole = parts.shift() || '';
            let decimals = parts.length ? '.' + parts.join('').slice(0, 2) : '';

            whole = whole.replace(/^0+(?=\d)/, '');
            whole = whole.replace(/\B(?=(\d{3})+(?!\d))/g, ',');

            return whole + decimals;
        },

        formatUnitPriceInput(event) {
            event.target.value = this.formatUnitPriceValue(event.target.value);
        },

        cleanUnitPrices(form) {
            form.querySelectorAll('.unit-price-input').forEach((input) => {
                input.value = String(input.value ?? '').replace(/,/g, '');
            });
        }
    }"
    x-init="$nextTick(() => $el.querySelectorAll('.unit-price-input').forEach((input) => input.value = formatUnitPriceValue(input.value)))"
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

                    <a
                        href="{{ route('admin.devices.checklist.create', $device) }}"
                        target="_blank"
                        rel="noopener"
                        class="rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700"
                    >
                        Mark as Checked
                    </a>

                    <button
                        type="button"
                        x-on:click.prevent.stop="editOpen = true"
                        class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
                    >
                        Edit
                    </button>

                    @if(auth()->user()->isAdmin())
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
                    @endif
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
                    <div class="text-sm text-gray-500">Computer Name</div>
                    <div class="font-medium text-gray-900">
                        {{ $device->computer_name ?: '-' }}
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

                    @if($deviceTypeName === 'desktop')
                        <div>
                            <div class="text-sm text-gray-500">Form Factor</div>
                            <div class="font-medium text-gray-900">
                                {{ data_get($device->specs, 'form_factor', '-') ?: '-' }}
                            </div>
                        </div>
                    @endif
                    <div>
                        <div class="text-sm text-gray-500">OS Version</div>
                        <div class="font-medium text-gray-900">{{ $device->os_version ?: '-' }}</div>
                    </div>

                    <div>
                        <div class="text-sm text-gray-500">OS License</div>
                        <div class="font-medium text-gray-900">{{ $device->os_license ?: '-' }}</div>
                    </div>

                    <div>
                        <div class="text-sm text-gray-500">MS Office Version</div>
                        <div class="font-medium text-gray-900">{{ $device->ms_office_version ?: '-' }}</div>
                    </div>

                    <div>
                        <div class="text-sm text-gray-500">MS Office License</div>
                        <div class="font-medium text-gray-900">{{ $device->ms_office_license ?: '-' }}</div>
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
    <div
        x-show="editOpen"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4"
    >
        <div
            x-show="editOpen"
            x-transition
            @click.away="editOpen = false"
            class="w-full max-w-4xl overflow-hidden rounded-2xl bg-white shadow-xl"
        >
            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900">Edit Device</h2>
                <button
                    type="button"
                    x-on:click="editOpen = false"
                    class="rounded-lg px-3 py-1 text-xl text-gray-500 hover:bg-gray-100 hover:text-gray-700"
                >
                    &times;
                </button>
            </div>

            <form method="POST" action="{{ route('admin.devices.update', $device) }}" class="space-y-4" x-on:submit="cleanUnitPrices($event.target)">
            @csrf
            @method('PUT')

            <input type="hidden" name="status" value="{{ $device->status ?? 'available' }}">

            <div class="max-h-[75vh] overflow-y-auto px-6 py-5">
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
                        maxlength="50"
                        pattern="[A-Za-z0-9][A-Za-z0-9\-\/]*"
                        title="Letters, numbers, hyphens, and slashes only"
                    >
                </div>

                <div>
                    <label class="text-sm font-medium">Serial Number</label>
                    <input
                        name="serial_number"
                        value="{{ old('serial_number', $device->serial_number) }}"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        maxlength="100"
                        pattern="[A-Za-z0-9\-]*"
                        title="Letters, numbers, and hyphens only"
                        placeholder="Enter serial number"
                    >
                </div>

                <div>
                    <label class="text-sm font-medium">Computer Name</label>
                    <input
                        name="computer_name"
                        value="{{ old('computer_name', $device->computer_name) }}"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        maxlength="100"
                        pattern="[A-Za-z0-9][A-Za-z0-9\-\s]*"
                        title="Letters, numbers, hyphens, and spaces only"
                        placeholder="Enter computer name"
                    >
                </div>

                <div>
                    <label class="text-sm font-medium">Brand</label>
                    <input
                        name="brand"
                        value="{{ old('brand', $device->brand) }}"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        maxlength="100"
                        pattern="[A-Za-zÑñ0-9][A-Za-zÑñ0-9.\-\s]*"
                        title="Letters and numbers only"
                    >
                </div>

                <div>
                    <label class="text-sm font-medium">Model</label>
                    <input
                        name="model"
                        value="{{ old('model', $device->model) }}"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        maxlength="100"
                        pattern="[A-Za-z0-9][A-Za-z0-9.\-\/\s]*"
                        title="Letters and numbers only"
                    >
                </div>

                <div x-show="isComputerType()" x-cloak>
                    <label class="text-sm font-medium">MAC Address</label>
                    <input
                        name="mac_address"
                        value="{{ old('mac_address', $device->mac_address) }}"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        maxlength="17"
                        pattern="[0-9A-Fa-f]{2}(:[0-9A-Fa-f]{2}){5}"
                        title="Format: 00:1A:2B:3C:4D:5E"
                        placeholder="00:1A:2B:3C:4D:5E"
                        :disabled="!isComputerType()"
                    >
                </div>



                <div x-show="isComputerType()" x-cloak>
                    <label class="text-sm font-medium">Memory</label>
                    <input
                        name="specs[memory]"
                        value="{{ old('specs.memory', data_get($device->specs, 'memory')) }}"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        maxlength="50"
                        :disabled="!isComputerType()"
                    >
                </div>

                <div x-show="isComputerType()" x-cloak>
                    <label class="text-sm font-medium">Storage</label>
                    <input
                        name="specs[storage]"
                        value="{{ old('specs.storage', data_get($device->specs, 'storage')) }}"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        maxlength="50"
                        :disabled="!isComputerType()"
                    >
                </div>

                <div x-show="isDesktopType()" x-cloak>
                    <label class="text-sm font-medium">Form Factor</label>
                    <select
                        name="specs[form_factor]"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        :disabled="!isDesktopType()"
                    >
                        <option value="">-- Select Form Factor --</option>
                        <option value="Tower Desktops" @selected(old('specs.form_factor', data_get($device->specs, 'form_factor')) === 'Tower Desktops')>Tower Desktops</option>
                        <option value="Small Form Factor (SFF) Desktops" @selected(old('specs.form_factor', data_get($device->specs, 'form_factor')) === 'Small Form Factor (SFF) Desktops')>Small Form Factor (SFF) Desktops</option>
                        <option value="All-in-One (AIO) Desktops" @selected(old('specs.form_factor', data_get($device->specs, 'form_factor')) === 'All-in-One (AIO) Desktops')>All-in-One (AIO) Desktops</option>
                        <option value="Mini PCs" @selected(old('specs.form_factor', data_get($device->specs, 'form_factor')) === 'Mini PCs')>Mini PCs</option>
                        <option value="Workstations" @selected(old('specs.form_factor', data_get($device->specs, 'form_factor')) === 'Workstations')>Workstations</option>
                    </select>
                </div>

                {{-- OS Version --}}
                <div id="show_os_version_wrapper" style="display:none;">
                    <label class="text-sm font-medium">OS Version</label>
                    <select name="os_version" id="show_os_version_select" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2">
                        <option value="">-- Select OS --</option>
                        <option value="Windows 7" {{ old('os_version', $device->os_version) === 'Windows 7' ? 'selected' : '' }}>Windows 7</option>
                        <option value="Windows 8" {{ old('os_version', $device->os_version) === 'Windows 8' ? 'selected' : '' }}>Windows 8</option>
                        <option value="Windows 10" {{ old('os_version', $device->os_version) === 'Windows 10' ? 'selected' : '' }}>Windows 10</option>
                        <option value="Windows 11" {{ old('os_version', $device->os_version) === 'Windows 11' ? 'selected' : '' }}>Windows 11</option>
                    </select>
                </div>

                {{-- OS License --}}
                <div id="show_os_license_wrapper" style="display:none;">
                    <label class="text-sm font-medium">OS License</label>
                    <select name="os_license" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2">
                        <option value="">-- Select License --</option>
                        <option value="Cracked" {{ old('os_license', $device->os_license) === 'Cracked' ? 'selected' : '' }}>Cracked</option>
                        <option value="OEM Licensed" {{ old('os_license', $device->os_license) === 'OEM Licensed' ? 'selected' : '' }}>OEM Licensed</option>
                    </select>
                </div>

                {{-- MS Office Version --}}
                <div id="show_ms_version_wrapper" style="display:none;">
                    <label class="text-sm font-medium">MS Office Version</label>
                    <select name="ms_office_version" id="show_ms_version_select" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2">
                        <option value="">-- Select MS Office --</option>
                        <option value="Office 2007" {{ old('ms_office_version', $device->ms_office_version) === 'Office 2007' ? 'selected' : '' }}>Office 2007</option>
                        <option value="Office 2010" {{ old('ms_office_version', $device->ms_office_version) === 'Office 2010' ? 'selected' : '' }}>Office 2010</option>
                        <option value="Office 2013" {{ old('ms_office_version', $device->ms_office_version) === 'Office 2013' ? 'selected' : '' }}>Office 2013</option>
                        <option value="Office 2016" {{ old('ms_office_version', $device->ms_office_version) === 'Office 2016' ? 'selected' : '' }}>Office 2016</option>
                        <option value="Office 2019" {{ old('ms_office_version', $device->ms_office_version) === 'Office 2019' ? 'selected' : '' }}>Office 2019</option>
                        <option value="Office 2021" {{ old('ms_office_version', $device->ms_office_version) === 'Office 2021' ? 'selected' : '' }}>Office 2021</option>
                        <option value="Microsoft 365" {{ old('ms_office_version', $device->ms_office_version) === 'Microsoft 365' ? 'selected' : '' }}>Microsoft 365</option>
                    </select>
                </div>

                {{-- MS Office License --}}
                <div id="show_ms_license_wrapper" style="display:none;">
                    <label class="text-sm font-medium">MS Office License</label>
                    <select name="ms_office_license" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2">
                        <option value="">-- Select License --</option>
                        <option value="Cracked" {{ old('ms_office_license', $device->ms_office_license) === 'Cracked' ? 'selected' : '' }}>Cracked</option>
                        <option value="OEM Licensed" {{ old('ms_office_license', $device->ms_office_license) === 'OEM Licensed' ? 'selected' : '' }}>OEM Licensed</option>
                    </select>
                </div>


                <div>
                    <label class="text-sm font-medium">Unit Price</label>
                    <input
                        name="unit_price"
                        type="text"
                        inputmode="decimal"
                        value="{{ old('unit_price', $device->unit_price) }}"
                        placeholder="e.g. 25,000.00"
                        class="unit-price-input mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        x-on:input="formatUnitPriceInput($event)"
                    >
                </div>

                <div>
                    <label class="text-sm font-medium">Date Acquired</label>
                    <input
                        name="date_acquired"
                        type="date"
                        max="{{ now()->format('Y-m-d') }}"
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
                        max="{{ now()->format('Y-m-d') }}"
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
                    maxlength="1000"
                    class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                >{{ old('maintenance_remarks', $device->maintenance_remarks) }}</textarea>
            </div>

            <div>
                <label class="text-sm font-medium">Notes</label>
                <textarea
                    name="notes"
                    rows="3"
                    maxlength="2000"
                    class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                >{{ old('notes', $device->notes) }}</textarea>
            </div>

            </div>

            <div class="flex justify-end gap-2 border-t border-gray-200 px-6 py-4">
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
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    var typeName = @json(strtolower($device->type?->name ?? ''));
    var isComputer = typeName === 'desktop' || typeName === 'laptop';

    var osVerSel  = document.getElementById('show_os_version_select');
    var msVerSel  = document.getElementById('show_ms_version_select');
    var osVerWrap = document.getElementById('show_os_version_wrapper');
    var osLicWrap = document.getElementById('show_os_license_wrapper');
    var msVerWrap = document.getElementById('show_ms_version_wrapper');
    var msLicWrap = document.getElementById('show_ms_license_wrapper');

    function show(el) { if (el) el.style.display = ''; }
    function hide(el) { if (el) el.style.display = 'none'; }

    function init() {
        if (!isComputer) return;
        show(osVerWrap);
        show(msVerWrap);
        if (osVerSel && osVerSel.value) show(osLicWrap); else hide(osLicWrap);
        if (msVerSel && msVerSel.value) show(msLicWrap); else hide(msLicWrap);
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

    // Re-init when quick edit panel opens
    document.addEventListener('click', function () {
        setTimeout(init, 150);
    });

    init();
})();
</script>
@endpush
@endsection
