@extends('admin.layouts.app')

@section('title', 'Device Manager')
@section('page_title', 'Device Manager')

@section('content')

<div
    x-data="{
        addOpen: false,
        editOpen: false,
        deleteOpen: false,

        addTypeId: '{{ old('device_type_id', $types->first()?->id) }}',

        typeNames: @js($types->pluck('name', 'id')),

        editDevice: {
            id: null,
            device_type_id: '',
            property_number: '',
            serial_number: '',
            computer_name: '',
            brand: '',
            model: '',
            mac_address: '',
            unit_price: '',
            date_acquired: '',
            last_maintenance_date: '',
            maintenance_remarks: '',
            notes: '',
            status: 'available',
            condition: 'serviceable',
            specs: {
                os: '',
                memory: '',
                storage: '',
                form_factor: ''
            }
        },

        deleteDeviceId: null,

        getTypeName(typeId) {
            return (this.typeNames[typeId] || '').toLowerCase();
        },

        isComputerType(typeId) {
            let name = this.getTypeName(typeId);
            return name === 'desktop' || name === 'laptop';
        },

        isDesktopType(typeId) {
            return this.getTypeName(typeId) === 'desktop';
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
        },

        openEdit(device) {
            device.specs = device.specs ?? {};
            device.specs.os = device.specs.os ?? '';
            device.specs.memory = device.specs.memory ?? '';
            device.specs.storage = device.specs.storage ?? '';
            device.specs.form_factor = device.specs.form_factor ?? '';
            device.serial_number = device.serial_number ?? '';
            device.computer_name = device.computer_name ?? '';
            device.status = device.status ?? 'available';
            device.condition = device.condition ?? 'serviceable';
            device.unit_price = this.formatUnitPriceValue(device.unit_price);

            this.editDevice = device;
            this.editOpen = true;
        },

        openDelete(id) {
            this.deleteDeviceId = id;
            this.deleteOpen = true;
            this.$nextTick(() => this.$refs.confirmDeleteBtn && this.$refs.confirmDeleteBtn.focus());
        }
    }"
    x-init="$nextTick(() => $el.querySelectorAll('.unit-price-input').forEach((input) => input.value = formatUnitPriceValue(input.value)))"
    class="space-y-5"
>
    <div class="flex items-start justify-between gap-3">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">
                Equipment Manager
            </h1>
        </div>

        <div class="flex flex-wrap gap-2">
            <a
               href="{{ route('admin.devices.qr.index') }}"
               class="shrink-0 inline-flex items-center rounded-xl bg-green-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-green-700"
            >
                Generate QR
            </a>

            <a
                href="{{ route('admin.reports.preventiveMaintenance.export') }}"
                class="shrink-0 inline-flex items-center rounded-xl bg-violet-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-violet-700"
            >
                Export Excel Report
            </a>

            <button
                type="button"
                class="shrink-0 inline-flex items-center rounded-xl bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700"
                x-on:click="addOpen = true"
            >
                + Add Device
            </button>
        </div>
    </div>

    @if($errors->any())
        <div class="rounded-xl bg-red-100 px-4 py-3 text-sm text-red-700">
            <div class="font-semibold">Please check the form.</div>
            <ul class="mt-1 list-inside list-disc">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Filters --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
        <form method="GET" class="flex flex-col gap-3 lg:flex-row lg:items-center">
            <div class="lg:w-64">
                <select
                    name="type"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                >
                    <option value="">All device types</option>

                    @foreach($types as $t)
                        <option value="{{ $t->id }}" @selected((int)($typeId ?? 0) === $t->id)>
                            {{ $t->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="lg:w-64">
                <select
                    name="condition"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                >
                    <option value="">All conditions</option>
                    <option value="serviceable" @selected(($condition ?? '') === 'serviceable')>
                        Serviceable
                    </option>
                    <option value="unserviceable" @selected(($condition ?? '') === 'unserviceable')>
                        Unserviceable
                    </option>
                </select>
            </div>

            <input
                name="q"
                value="{{ $q }}"
                placeholder="Search property #, serial #..."
                class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
            >

            <div class="flex gap-2">
                <button
                    type="submit"
                    class="inline-flex items-center rounded-xl bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700"
                >
                    Search
                </button>

                <a
                    href="{{ route('admin.devices.index') }}"
                    class="inline-flex items-center rounded-xl bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200"
                >
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Mobile cards --}}
    <div class="grid grid-cols-1 gap-3 md:hidden">
        @forelse($devices as $d)
            @php
                $deviceTypeName = strtolower($d->type?->name ?? '');
                $isComputer = in_array($deviceTypeName, ['desktop', 'laptop']);
            @endphp

            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                <div class="space-y-3">
                    <div>
                        <div class="text-sm text-gray-500">Type</div>
                        <div class="font-semibold text-gray-900">
                            {{ $d->type?->name ?? '-' }}
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <div class="text-gray-500">Property #</div>
                            <div class="text-gray-900">{{ $d->property_number }}</div>
                        </div>

                        <div>
                            <div class="text-gray-500">Serial #</div>
                            <div class="text-gray-900">{{ $d->serial_number ?: '-' }}</div>
                        </div>

                        <div>
                            <div class="text-gray-500">Computer Name</div>
                            <div class="text-gray-900">{{ $d->computer_name ?: '-' }}</div>
                        </div>

                        <div>
                            <div class="text-gray-500">Acquired</div>
                            <div class="text-gray-900">
                                {{ $d->date_acquired ? $d->date_acquired->format('M d, Y') : '-' }}
                            </div>
                        </div>

                        <div>
                            <div class="text-gray-500">Condition</div>
                            <div class="text-gray-900 capitalize">
                                {{ $d->condition ?? 'serviceable' }}
                            </div>
                        </div>

                        @if($isComputer)
                            <div>
                                <div class="text-gray-500">MAC Address</div>
                                <div class="text-gray-900">
                                    {{ $d->mac_address ?: '-' }}
                                </div>
                            </div>



                            <div>
                                <div class="text-gray-500">Memory</div>
                                <div class="text-gray-900">
                                    {{ data_get($d->specs, 'memory', '-') ?: '-' }}
                                </div>
                            </div>

                            <div>
                                <div class="text-gray-500">Storage</div>
                                <div class="text-gray-900">
                                    {{ data_get($d->specs, 'storage', '-') ?: '-' }}
                                </div>
                            </div>

                            <div>
                                <div class="text-gray-500">Form Factor</div>
                                <div class="text-gray-900">
                                    {{ data_get($d->specs, 'form_factor', '-') ?: '-' }}
                                </div>
                            </div>
                        @endif

                        <div>
                            <div class="text-gray-500">Last Maintenance</div>
                            <div class="text-gray-900">
                                {{ $d->last_maintenance_date ? $d->last_maintenance_date->format('M d, Y') : 'Not yet checked' }}
                            </div>
                        </div>
                    </div>

                    @if($d->maintenance_remarks)
                        <div class="text-sm">
                            <div class="text-gray-500">Maintenance Remarks</div>
                            <div class="text-gray-900">{{ $d->maintenance_remarks }}</div>
                        </div>
                    @endif

                    <div class="flex flex-wrap gap-2 pt-1">
                        <a
                            href="{{ route('admin.devices.show', $d) }}"
                            class="rounded-lg bg-green-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-green-700"
                        >
                            View
                        </a>

                        <a
                            href="{{ route('admin.devices.history', $d) }}"
                            class="rounded-lg bg-purple-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-purple-700"
                        >
                            History
                        </a>

                        <form method="POST" action="{{ route('admin.devices.markChecked', $d) }}">
                            @csrf
                            @method('PATCH')

                            <button
                                type="submit"
                                class="rounded-lg bg-blue-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-blue-700"
                            >
                                Mark Checked
                            </button>
                        </form>

                        <button
                            type="button"
                            class="rounded-lg bg-gray-900 px-3 py-1.5 text-sm font-medium text-white hover:bg-black"
                            x-on:click="openEdit({
                                id: {{ $d->id }},
                                device_type_id: '{{ $d->device_type_id }}',
                                property_number: @js($d->property_number),
                                serial_number: @js($d->serial_number ?? ''),
                                computer_name: @js($d->computer_name ?? ''),
                                brand: @js($d->brand ?? ''),
                                model: @js($d->model ?? ''),
                                mac_address: @js($d->mac_address ?? ''),
                                unit_price: @js($d->unit_price ?? ''),
                                date_acquired: @js($d->date_acquired ? $d->date_acquired->format('Y-m-d') : ''),
                                last_maintenance_date: @js($d->last_maintenance_date ? $d->last_maintenance_date->format('Y-m-d') : ''),
                                maintenance_remarks: @js($d->maintenance_remarks ?? ''),
                                status: @js($d->status ?? 'available'),
                                condition: @js($d->condition ?? 'serviceable'),
                                notes: @js($d->notes ?? ''),
                                specs: {
                                    os: @js(data_get($d->specs, 'os', '')),
                                    memory: @js(data_get($d->specs, 'memory', '')),
                                    storage: @js(data_get($d->specs, 'storage', '')),
                                    form_factor: @js(data_get($d->specs, 'form_factor', ''))
                                }
                            })"
                        >
                            Edit
                        </button>

                        @if(auth()->user()->isAdmin())
                            <button
                                type="button"
                                class="rounded-lg bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700"
                                x-on:click="openDelete({{ $d->id }})"
                            >
                                Delete
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-2xl border border-gray-200 bg-white p-6 text-center text-gray-500 shadow-sm">
                No devices found.
            </div>
        @endforelse
    </div>

    {{-- Desktop table --}}
    <div class="hidden overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm md:block">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-left">
                    <tr>
                        <th class="px-4 py-3 font-semibold text-gray-700">Type</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Property #</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Serial #</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Acquired</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Last Maintenance</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Condition</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">
                    @forelse($devices as $d)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-900">
                                {{ $d->type?->name ?? '-' }}
                            </td>

                            <td class="px-4 py-3 text-gray-900">
                                {{ $d->property_number }}
                            </td>

                            <td class="px-4 py-3 text-gray-700">
                                {{ $d->serial_number ?: '-' }}
                            </td>

                            <td class="px-4 py-3 text-gray-700">
                                {{ $d->date_acquired ? $d->date_acquired->format('M d, Y') : '-' }}
                            </td>

                            <td class="px-4 py-3 text-gray-700">
                                @if($d->last_maintenance_date)
                                    <div class="font-medium text-gray-900">
                                        {{ $d->last_maintenance_date->format('M d, Y') }}
                                    </div>

                                    @if($d->maintenance_remarks)
                                        <div class="max-w-xs truncate text-xs text-gray-500">
                                            {{ $d->maintenance_remarks }}
                                        </div>
                                    @endif
                                @else
                                    <span class="text-gray-400">
                                        Not yet checked
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-gray-700 capitalize">
                                {{ $d->condition ?? 'serviceable' }}
                            </td>

                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <a
                                        href="{{ route('admin.devices.show', $d) }}"
                                        class="rounded-lg bg-green-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-green-700"
                                    >
                                        View
                                    </a>

                                    <a
                                        href="{{ route('admin.devices.history', $d) }}"
                                        class="rounded-lg bg-purple-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-purple-700"
                                    >
                                        History
                                    </a>

                                    <form method="POST" action="{{ route('admin.devices.markChecked', $d) }}">
                                        @csrf
                                        @method('PATCH')

                                        <button
                                            type="submit"
                                            class="rounded-lg bg-blue-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-blue-700"
                                        >
                                            Mark Checked
                                        </button>
                                    </form>

                                    <button
                                        type="button"
                                        class="rounded-lg bg-gray-900 px-3 py-1.5 text-sm font-medium text-white hover:bg-black"
                                        x-on:click="openEdit({
                                            id: {{ $d->id }},
                                            device_type_id: '{{ $d->device_type_id }}',
                                            property_number: @js($d->property_number),
                                            serial_number: @js($d->serial_number ?? ''),
                                            computer_name: @js($d->computer_name ?? ''),
                                            brand: @js($d->brand ?? ''),
                                            model: @js($d->model ?? ''),
                                            mac_address: @js($d->mac_address ?? ''),
                                            unit_price: @js($d->unit_price ?? ''),
                                            date_acquired: @js($d->date_acquired ? $d->date_acquired->format('Y-m-d') : ''),
                                            last_maintenance_date: @js($d->last_maintenance_date ? $d->last_maintenance_date->format('Y-m-d') : ''),
                                            maintenance_remarks: @js($d->maintenance_remarks ?? ''),
                                            status: @js($d->status ?? 'available'),
                                            condition: @js($d->condition ?? 'serviceable'),
                                            notes: @js($d->notes ?? ''),
                                            specs: {
                                                os: @js(data_get($d->specs, 'os', '')),
                                                memory: @js(data_get($d->specs, 'memory', '')),
                                                storage: @js(data_get($d->specs, 'storage', '')),
                                                form_factor: @js(data_get($d->specs, 'form_factor', ''))
                                            }
                                        })"
                                    >
                                        Edit
                                    </button>

                                    @if(auth()->user()->isAdmin())
                                        <button
                                            type="button"
                                            class="rounded-lg bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700"
                                            x-on:click="openDelete({{ $d->id }})"
                                        >
                                            Delete
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                No devices found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>
        {{ $devices->links() }}
    </div>

    {{-- ADD MODAL --}}
    <x-modal show="addOpen" title="Add Device">
        <form method="POST" action="{{ route('admin.devices.store') }}" class="space-y-4" x-on:submit="cleanUnitPrices($event.target)">
            @csrf

            <input type="hidden" name="status" value="available">

            <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                <div>
                    <label class="text-sm font-medium">Device Type</label>
                    <select
                        name="device_type_id"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        required
                        x-model="addTypeId"
                    >
                        @foreach($types as $t)
                            <option value="{{ $t->id }}">
                                {{ $t->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-sm font-medium">Property Number</label>
                    <input
                        name="property_number"
                        value="{{ old('property_number') }}"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        required
                        maxlength="50"
                        pattern="[A-Za-z0-9][A-Za-z0-9\-\/]*"
                        title="Letters, numbers, hyphens, and slashes only"
                        placeholder="e.g. PN-2026-0001"
                    >
                </div>

                <div>
                    <label class="text-sm font-medium">Serial Number</label>
                    <input
                        name="serial_number"
                        value="{{ old('serial_number') }}"
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
                        value="{{ old('computer_name') }}"
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
                        value="{{ old('brand') }}"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        maxlength="100"
                        pattern="[A-Za-zÑñ0-9][A-Za-zÑñ0-9.\-\s]*"
                        title="Letters and numbers only"
                        placeholder="e.g. HP, Dell, ASUS"
                    >
                </div>

                <div>
                    <label class="text-sm font-medium">Model</label>
                    <input
                        name="model"
                        value="{{ old('model') }}"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        maxlength="100"
                        pattern="[A-Za-z0-9][A-Za-z0-9.\-\/\s]*"
                        title="Letters and numbers only"
                        placeholder="Example: Epson L3110, Acer Aspire"
                    >
                </div>

                <div x-show="isComputerType(addTypeId)" x-cloak>
                    <label class="text-sm font-medium">MAC Address</label>
                    <input
                        name="mac_address"
                        value="{{ old('mac_address') }}"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        maxlength="17"
                        pattern="[0-9A-Fa-f]{2}(:[0-9A-Fa-f]{2}){5}"
                        title="Format: 00:1A:2B:3C:4D:5E"
                        placeholder="00:1A:2B:3C:4D:5E"
                        :disabled="!isComputerType(addTypeId)"
                    >
                </div>


                <div x-show="isComputerType(addTypeId)" x-cloak>
                    <label class="text-sm font-medium">Memory</label>
                    <input
                        name="specs[memory]"
                        value="{{ old('specs.memory') }}"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        maxlength="50"
                        placeholder="Example: 8GB RAM"
                        :disabled="!isComputerType(addTypeId)"
                    >
                </div>

                <div x-show="isComputerType(addTypeId)" x-cloak>
                    <label class="text-sm font-medium">Storage</label>
                    <input
                        name="specs[storage]"
                        value="{{ old('specs.storage') }}"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        maxlength="50"
                        placeholder="Example: 256GB SSD / 1TB HDD"
                        :disabled="!isComputerType(addTypeId)"
                    >
                </div>

                <div x-show="isDesktopType(addTypeId)" x-cloak>
                    <label class="text-sm font-medium">Form Factor</label>
                    <select
                        name="specs[form_factor]"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        :disabled="!isDesktopType(addTypeId)"
                    >
                        <option value="">-- Select Form Factor --</option>
                        <option value="Tower Desktops" @selected(old('specs.form_factor') === 'Tower Desktops')>Tower Desktops</option>
                        <option value="Small Form Factor (SFF) Desktops" @selected(old('specs.form_factor') === 'Small Form Factor (SFF) Desktops')>Small Form Factor (SFF) Desktops</option>
                        <option value="All-in-One (AIO) Desktops" @selected(old('specs.form_factor') === 'All-in-One (AIO) Desktops')>All-in-One (AIO) Desktops</option>
                        <option value="Mini PCs" @selected(old('specs.form_factor') === 'Mini PCs')>Mini PCs</option>
                        <option value="Workstations" @selected(old('specs.form_factor') === 'Workstations')>Workstations</option>
                    </select>
                </div>

                {{-- OS Version --}}
                <div id="add_os_version_wrapper" style="display:none;">
                    <label class="text-sm font-medium">OS Version</label>
                    <select name="os_version" id="add_os_version_select" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2">
                        <option value="">-- Select OS --</option>
                        <option value="Windows 7">Windows 7</option>
                        <option value="Windows 8">Windows 8</option>
                        <option value="Windows 10">Windows 10</option>
                        <option value="Windows 11">Windows 11</option>
                    </select>
                </div>

                {{-- OS License --}}
                <div id="add_os_license_wrapper" style="display:none;">
                    <label class="text-sm font-medium">OS License</label>
                    <select name="os_license" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2">
                        <option value="">-- Select License --</option>
                        <option value="Cracked">Cracked</option>
                        <option value="OEM Licensed">OEM Licensed</option>
                    </select>
                </div>

                {{-- MS Office Version --}}
                <div id="add_ms_version_wrapper" style="display:none;">
                    <label class="text-sm font-medium">MS Office Version</label>
                    <select name="ms_office_version" id="add_ms_version_select" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2">
                        <option value="">-- Select MS Office --</option>
                        <option value="Office 2007">Office 2007</option>
                        <option value="Office 2010">Office 2010</option>
                        <option value="Office 2013">Office 2013</option>
                        <option value="Office 2016">Office 2016</option>
                        <option value="Office 2019">Office 2019</option>
                        <option value="Office 2021">Office 2021</option>
                        <option value="Microsoft 365">Microsoft 365</option>
                    </select>
                </div>

                {{-- MS Office License --}}
                <div id="add_ms_license_wrapper" style="display:none;">
                    <label class="text-sm font-medium">MS Office License</label>
                    <select name="ms_office_license" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2">
                        <option value="">-- Select License --</option>
                        <option value="Cracked">Cracked</option>
                        <option value="OEM Licensed">OEM Licensed</option>
                    </select>
                </div>

                <div>
                    <label class="text-sm font-medium">Unit Price</label>
                    <input
                        name="unit_price"
                        value="{{ old('unit_price') }}"
                        type="text"
                        inputmode="decimal"
                        placeholder="e.g. 25,000.00"
                        class="unit-price-input mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        x-on:input="formatUnitPriceInput($event)"
                    >
                </div>

                <div>
                    <label class="text-sm font-medium">Date Acquired</label>
                    <input
                        name="date_acquired"
                        value="{{ old('date_acquired') }}"
                        type="date"
                        max="{{ now()->format('Y-m-d') }}"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                    >
                </div>

                <div>
                    <label class="text-sm font-medium">Condition</label>
                    <select
                        name="condition"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                    >
                        <option value="serviceable" @selected(old('condition', 'serviceable') === 'serviceable')>
                            Serviceable
                        </option>
                        <option value="unserviceable" @selected(old('condition') === 'unserviceable')>
                            Unserviceable
                        </option>
                    </select>
                </div>

                <div>
                    <label class="text-sm font-medium">Last Maintenance Date</label>
                    <input
                        name="last_maintenance_date"
                        value="{{ old('last_maintenance_date') }}"
                        type="date"
                        max="{{ now()->format('Y-m-d') }}"
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
                    placeholder="Example: Initial check, cleaned, inspected"
                >{{ old('maintenance_remarks') }}</textarea>
            </div>

            <div>
                <label class="text-sm font-medium">Notes</label>
                <textarea
                    name="notes"
                    rows="3"
                    maxlength="2000"
                    class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                >{{ old('notes') }}</textarea>
            </div>

            <div class="flex gap-2 pt-2">
                <button
                    type="submit"
                    class="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700"
                >
                    Save
                </button>

                <button
                    type="button"
                    class="rounded-lg bg-gray-100 px-4 py-2 text-gray-700 hover:bg-gray-200"
                    x-on:click="addOpen = false"
                >
                    Cancel
                </button>
            </div>
        </form>
    </x-modal>

    {{-- EDIT MODAL --}}
    <x-modal show="editOpen" title="Edit Device">
        <form method="POST" :action="`{{ url('/admin/devices') }}/${editDevice.id}`" class="space-y-4" x-on:submit="cleanUnitPrices($event.target)">
            @csrf
            @method('PUT')

            <input type="hidden" name="status" x-model="editDevice.status">

            <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                <div>
                    <label class="text-sm font-medium">Device Type</label>
                    <select
                        name="device_type_id"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        required
                        x-model="editDevice.device_type_id"
                    >
                        @foreach($types as $t)
                            <option value="{{ $t->id }}">
                                {{ $t->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-sm font-medium">Property Number</label>
                    <input
                        name="property_number"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        x-model="editDevice.property_number"
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
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        x-model="editDevice.serial_number"
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
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        x-model="editDevice.computer_name"
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
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        x-model="editDevice.brand"
                        maxlength="100"
                        pattern="[A-Za-zÑñ0-9][A-Za-zÑñ0-9.\-\s]*"
                        title="Letters and numbers only"
                    >
                </div>

                <div>
                    <label class="text-sm font-medium">Model</label>
                    <input
                        name="model"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        x-model="editDevice.model"
                        maxlength="100"
                        pattern="[A-Za-z0-9][A-Za-z0-9.\-\/\s]*"
                        title="Letters and numbers only"
                        placeholder="Example: Epson L3110, Acer Aspire"
                    >
                </div>

                <div x-show="isComputerType(editDevice.device_type_id)" x-cloak>
                    <label class="text-sm font-medium">MAC Address</label>
                    <input
                        name="mac_address"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        x-model="editDevice.mac_address"
                        maxlength="17"
                        pattern="[0-9A-Fa-f]{2}(:[0-9A-Fa-f]{2}){5}"
                        title="Format: 00:1A:2B:3C:4D:5E"
                        placeholder="00:1A:2B:3C:4D:5E"
                        :disabled="!isComputerType(editDevice.device_type_id)"
                    >
                </div>

                <div x-show="isComputerType(editDevice.device_type_id)" x-cloak>
                    <label class="text-sm font-medium">Memory</label>
                    <input
                        name="specs[memory]"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        x-model="editDevice.specs.memory"
                        maxlength="50"
                        placeholder="Example: 8GB RAM"
                        :disabled="!isComputerType(editDevice.device_type_id)"
                    >
                </div>

                <div x-show="isComputerType(editDevice.device_type_id)" x-cloak>
                    <label class="text-sm font-medium">Storage</label>
                    <input
                        name="specs[storage]"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        x-model="editDevice.specs.storage"
                        maxlength="50"
                        placeholder="Example: 256GB SSD / 1TB HDD"
                        :disabled="!isComputerType(editDevice.device_type_id)"
                    >
                </div>

                <div x-show="isDesktopType(editDevice.device_type_id)" x-cloak>
                    <label class="text-sm font-medium">Form Factor</label>
                    <select
                        name="specs[form_factor]"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        x-model="editDevice.specs.form_factor"
                        :disabled="!isDesktopType(editDevice.device_type_id)"
                    >
                        <option value="">-- Select Form Factor --</option>
                        <option value="Tower Desktops">Tower Desktops</option>
                        <option value="Small Form Factor (SFF) Desktops">Small Form Factor (SFF) Desktops</option>
                        <option value="All-in-One (AIO) Desktops">All-in-One (AIO) Desktops</option>
                        <option value="Mini PCs">Mini PCs</option>
                        <option value="Workstations">Workstations</option>
                    </select>
                </div>

                {{-- OS Version --}}
                <div id="edit_os_version_wrapper" style="display:none;">
                    <label class="text-sm font-medium">OS Version</label>
                    <select name="os_version" id="edit_os_version_select" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2">
                        <option value="">-- Select OS --</option>
                        <option value="Windows 7">Windows 7</option>
                        <option value="Windows 8">Windows 8</option>
                        <option value="Windows 10">Windows 10</option>
                        <option value="Windows 11">Windows 11</option>
                    </select>
                </div>

                {{-- OS License --}}
                <div id="edit_os_license_wrapper" style="display:none;">
                    <label class="text-sm font-medium">OS License</label>
                    <select name="os_license" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2">
                        <option value="">-- Select License --</option>
                        <option value="Cracked">Cracked</option>
                        <option value="OEM Licensed">OEM Licensed</option>
                    </select>
                </div>

                {{-- MS Office Version --}}
                <div id="edit_ms_version_wrapper" style="display:none;">
                    <label class="text-sm font-medium">MS Office Version</label>
                    <select name="ms_office_version" id="edit_ms_version_select" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2">
                        <option value="">-- Select MS Office --</option>
                        <option value="Office 2007">Office 2007</option>
                        <option value="Office 2010">Office 2010</option>
                        <option value="Office 2013">Office 2013</option>
                        <option value="Office 2016">Office 2016</option>
                        <option value="Office 2019">Office 2019</option>
                        <option value="Office 2021">Office 2021</option>
                        <option value="Microsoft 365">Microsoft 365</option>
                    </select>
                </div>

                {{-- MS Office License --}}
                <div id="edit_ms_license_wrapper" style="display:none;">
                    <label class="text-sm font-medium">MS Office License</label>
                    <select name="ms_office_license" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2">
                        <option value="">-- Select License --</option>
                        <option value="Cracked">Cracked</option>
                        <option value="OEM Licensed">OEM Licensed</option>
                    </select>
                </div>

                <div>
                    <label class="text-sm font-medium">Unit Price</label>
                    <input
                        name="unit_price"
                        type="text"
                        inputmode="decimal"
                        placeholder="e.g. 25,000.00"
                        class="unit-price-input mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        x-model="editDevice.unit_price"
                        x-on:input="formatUnitPriceInput($event)"
                    >
                </div>

                <div>
                    <label class="text-sm font-medium">Date Acquired</label>
                    <input
                        name="date_acquired"
                        type="date"
                        max="{{ now()->format('Y-m-d') }}"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        x-model="editDevice.date_acquired"
                    >
                </div>

                <div>
                    <label class="text-sm font-medium">Condition</label>
                    <select
                        name="condition"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        x-model="editDevice.condition"
                    >
                        <option value="serviceable">Serviceable</option>
                        <option value="unserviceable">Unserviceable</option>
                    </select>
                </div>

                <div>
                    <label class="text-sm font-medium">Last Maintenance Date</label>
                    <input
                        name="last_maintenance_date"
                        type="date"
                        max="{{ now()->format('Y-m-d') }}"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        x-model="editDevice.last_maintenance_date"
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
                    x-model="editDevice.maintenance_remarks"
                ></textarea>
            </div>

            <div>
                <label class="text-sm font-medium">Notes</label>
                <textarea
                    name="notes"
                    rows="3"
                    maxlength="2000"
                    class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                    x-model="editDevice.notes"
                ></textarea>
            </div>

            <div class="flex gap-2 pt-2">
                <button
                    type="submit"
                    class="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700"
                >
                    Save Changes
                </button>

                <button
                    type="button"
                    class="rounded-lg bg-gray-100 px-4 py-2 text-gray-700 hover:bg-gray-200"
                    x-on:click="editOpen = false"
                >
                    Cancel
                </button>
            </div>
        </form>
    </x-modal>

    {{-- DELETE MODAL --}}
    <x-modal show="deleteOpen" title="Delete Device">
        <div class="space-y-3">
            <div class="text-sm text-gray-700">
                Are you sure you want to delete this device?
            </div>

            <form
                method="POST"
                :action="`{{ url('/admin/devices') }}/${deleteDeviceId}`"
                @submit="if (!deleteDeviceId) $event.preventDefault()"
                class="flex gap-2"
            >
                @csrf
                @method('DELETE')

                <button
                    type="submit"
                    x-ref="confirmDeleteBtn"
                    class="rounded-lg bg-red-600 px-4 py-2 text-white hover:bg-red-700"
                >
                    Confirm
                </button>

                <button
                    type="button"
                    class="rounded-lg bg-gray-100 px-4 py-2 text-gray-700 hover:bg-gray-200"
                    x-on:click="deleteOpen = false"
                >
                    Cancel
                </button>
            </form>
        </div>
    </x-modal>
</div>
@push('scripts')
<script>
(function () {
    // ── ADD MODAL ──────────────────────────────────────────────
    var typeNames = @json($types->pluck('name', 'id'));

    function getTypeName(typeId) {
        return (typeNames[typeId] || '').toLowerCase();
    }

    function isComputer(typeId) {
        var name = getTypeName(typeId);
        return name === 'desktop' || name === 'laptop';
    }

    function show(el) { if (el) el.style.display = ''; }
    function hide(el) { if (el) el.style.display = 'none'; }

    // --- ADD modal ---
    var addTypeSelect    = document.querySelector('[x-model="addTypeId"]');
    var addOsVerSel      = document.getElementById('add_os_version_select');
    var addMsVerSel      = document.getElementById('add_ms_version_select');
    var addOsVerWrap     = document.getElementById('add_os_version_wrapper');
    var addOsLicWrap     = document.getElementById('add_os_license_wrapper');
    var addMsVerWrap     = document.getElementById('add_ms_version_wrapper');
    var addMsLicWrap     = document.getElementById('add_ms_license_wrapper');

    function updateAddFields() {
        var typeId = addTypeSelect ? addTypeSelect.value : '';
        var computer = isComputer(typeId);
        computer ? show(addOsVerWrap) : hide(addOsVerWrap);
        computer ? show(addMsVerWrap) : hide(addMsVerWrap);
        if (computer && addOsVerSel && addOsVerSel.value) { show(addOsLicWrap); } else { hide(addOsLicWrap); }
        if (computer && addMsVerSel && addMsVerSel.value) { show(addMsLicWrap); } else { hide(addMsLicWrap); }
    }

    if (addTypeSelect) {
        addTypeSelect.addEventListener('change', updateAddFields);
        // Also watch via MutationObserver since Alpine controls the value
        new MutationObserver(updateAddFields).observe(addTypeSelect, { attributes: true, childList: true, subtree: true });
    }
    if (addOsVerSel) {
        addOsVerSel.addEventListener('change', function () {
            this.value ? show(addOsLicWrap) : hide(addOsLicWrap);
        });
    }
    if (addMsVerSel) {
        addMsVerSel.addEventListener('change', function () {
            this.value ? show(addMsLicWrap) : hide(addMsLicWrap);
        });
    }

    // --- EDIT modal ---
    var editTypeSelect   = document.querySelector('[x-model="editDevice.device_type_id"]');
    var editOsVerSel     = document.getElementById('edit_os_version_select');
    var editMsVerSel     = document.getElementById('edit_ms_version_select');
    var editOsVerWrap    = document.getElementById('edit_os_version_wrapper');
    var editOsLicWrap    = document.getElementById('edit_os_license_wrapper');
    var editMsVerWrap    = document.getElementById('edit_ms_version_wrapper');
    var editMsLicWrap    = document.getElementById('edit_ms_license_wrapper');

    function updateEditFields() {
        var typeId = editTypeSelect ? editTypeSelect.value : '';
        var computer = isComputer(typeId);
        computer ? show(editOsVerWrap) : hide(editOsVerWrap);
        computer ? show(editMsVerWrap) : hide(editMsVerWrap);
        if (computer && editOsVerSel && editOsVerSel.value) { show(editOsLicWrap); } else { hide(editOsLicWrap); }
        if (computer && editMsVerSel && editMsVerSel.value) { show(editMsLicWrap); } else { hide(editMsLicWrap); }
    }

    if (editTypeSelect) {
        editTypeSelect.addEventListener('change', updateEditFields);
        new MutationObserver(updateEditFields).observe(editTypeSelect, { attributes: true, childList: true, subtree: true });
    }
    if (editOsVerSel) {
        editOsVerSel.addEventListener('change', function () {
            this.value ? show(editOsLicWrap) : hide(editOsLicWrap);
        });
    }
    if (editMsVerSel) {
        editMsVerSel.addEventListener('change', function () {
            this.value ? show(editMsLicWrap) : hide(editMsLicWrap);
        });
    }

    // Run on load
    updateAddFields();
    updateEditFields();

    // Re-run when modals open (Alpine toggles visibility)
    document.addEventListener('click', function () {
        setTimeout(function () {
            updateAddFields();
            updateEditFields();
        }, 100);
    });
})();
</script>
@endpush
@endsection
