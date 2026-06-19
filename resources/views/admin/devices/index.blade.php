@extends('admin.layouts.app')

@section('title', 'Device Manager')
@section('page_title', 'Device Manager')

@section('content')

<h1 style="color:red">TEST</h1>

<div
    x-data="{
        addOpen: false,
        editOpen: false,
        deleteOpen: false,

        addTypeId: '{{ old('device_type_id', $types->first()?->id) }}',

        addOs: '{{ old('specs.os_version', '') }}',
        addOsLicense: '{{ old('specs.os_license', '') }}',
        addOffice: '{{ old('specs.office_version', '') }}',
        addOfficeLicense: '{{ old('specs.office_license', '') }}',

        typeNames: @js($types->pluck('name', 'id')),

        editDevice: {
            id: null,
            device_type_id: '',
            property_number: '',
            serial_number: '',
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
                os_version: '',
                os_license: '',
                memory: '',
                storage: '',
                form_factor: '',
                office_version: '',
                office_license: ''
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

        openEdit(device) {
            device.specs = device.specs ?? {};
            device.specs.os = device.specs.os ?? '';
            device.specs.os_version = device.specs.os_version ?? '';
            device.specs.os_license = device.specs.os_license ?? '';
            device.specs.memory = device.specs.memory ?? '';
            device.specs.storage = device.specs.storage ?? '';
            device.specs.form_factor = device.specs.form_factor ?? '';
            device.specs.office_version = device.specs.office_version ?? '';
            device.specs.office_license = device.specs.office_license ?? '';
            device.serial_number = device.serial_number ?? '';
            device.status = device.status ?? 'available';
            device.condition = device.condition ?? 'serviceable';

            this.editDevice = device;
            this.editOpen = true;
        },

        openDelete(id) {
            this.deleteDeviceId = id;
            this.deleteOpen = true;
        }
    }"
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
                class="shrink-0 inline-flex items-center rounded-xl bg-green-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-green-700"
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

    @if(session('success'))
        <div class="rounded-xl bg-green-100 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-xl bg-red-100 px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

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
                                <div class="text-gray-500">Operating System</div>
                                <div class="text-gray-900">
                                    {{ data_get($d->specs, 'os_version', '-') ?: '-' }}
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

                            <div>
                                <div class="text-gray-500">Microsoft Office</div>
                                <div class="text-gray-900">
                                    {{ data_get($d->specs, 'office_version', '-') ?: '-' }}
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
                                    os_version: @js(data_get($d->specs, 'os_version', '')),
                                    os_license: @js(data_get($d->specs, 'os_license', '')),
                                    memory: @js(data_get($d->specs, 'memory', '')),
                                    storage: @js(data_get($d->specs, 'storage', '')),
                                    form_factor: @js(data_get($d->specs, 'form_factor', '')),
                                    office_version: @js(data_get($d->specs, 'office_version', '')),
                                    office_license: @js(data_get($d->specs, 'office_license', ''))
                                }
                            })"
                        >
                            Edit
                        </button>

                        <button
                            type="button"
                            class="rounded-lg bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700"
                            x-on:click="openDelete({{ $d->id }})"
                        >
                            Delete
                        </button>
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
                        os_version: @js(data_get($d->specs, 'os_version', '')),
                        os_license: @js(data_get($d->specs, 'os_license', '')),
                        memory: @js(data_get($d->specs, 'memory', '')),
                        storage: @js(data_get($d->specs, 'storage', '')),
                        form_factor: @js(data_get($d->specs, 'form_factor', '')),
                        office_version: @js(data_get($d->specs, 'office_version', '')),
                        office_license: @js(data_get($d->specs, 'office_license', ''))
                    }
                                        })"
                                    >
                                        Edit
                                    </button>

                                    <button
                                        type="button"
                                        class="rounded-lg bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700"
                                        x-on:click="openDelete({{ $d->id }})"
                                    >
                                        Delete
                                    </button>
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
        <form method="POST" action="{{ route('admin.devices.store') }}" class="space-y-4">
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
                    >
                </div>

                <div>
                    <label class="text-sm font-medium">Serial Number</label>
                    <input
                        name="serial_number"
                        value="{{ old('serial_number') }}"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        placeholder="Enter serial number"
                    >
                </div>

                <div>
                    <label class="text-sm font-medium">Brand</label>
                    <input
                        name="brand"
                        value="{{ old('brand') }}"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                    >
                </div>

                <div>
                    <label class="text-sm font-medium">Model</label>
                    <input
                        name="model"
                        value="{{ old('model') }}"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        placeholder="Example: Epson L3110, Acer Aspire"
                    >
                </div>

                <div x-show="isComputerType(addTypeId)" x-cloak>
                    <label class="text-sm font-medium">MAC Address</label>
                    <input
                        name="mac_address"
                        value="{{ old('mac_address') }}"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        placeholder="00:1A:2B:3C:4D:5E"
                        :disabled="!isComputerType(addTypeId)"
                    >
                </div>

                <div x-show="isComputerType(addTypeId)" x-cloak>
                    <label class="text-sm font-medium">Operating System</label>
                    <select
                        name="specs[os_version]"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        x-model="addOs"
                        :disabled="!isComputerType(addTypeId)"
                    >
                        <option value="">-- Select OS --</option>
                        <option value="Windows 7">Windows 7</option>
                        <option value="Windows 8">Windows 8</option>
                        <option value="Windows 10">Windows 10</option>
                        <option value="Windows 11">Windows 11</option>
                    </select>
                </div>

                <div x-show="isComputerType(addTypeId) && addOs !== ''" x-cloak>
                    <label class="text-sm font-medium">OS License Type</label>
                    <select
                        name="specs[os_license]"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        x-model="addOsLicense"
                        :disabled="!isComputerType(addTypeId) || addOs === ''"
                    >
                        <option value="">-- Select License --</option>
                        <option value="Cracked">Cracked</option>
                        <option value="OEM Licensed">OEM Licensed</option>
                        <option value="Retail Licensed">Retail Licensed</option>
                    </select>
                </div>

                <div x-show="isComputerType(addTypeId)" x-cloak>
                    <label class="text-sm font-medium">Microsoft Office Version</label>
                    <select
                        name="specs[office_version]"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        x-model="addOffice"
                        :disabled="!isComputerType(addTypeId)"
                    >
                        <option value="">-- Select Office Version --</option>
                        <option value="Office 2007">Office 2007</option>
                        <option value="Office 2010">Office 2010</option>
                        <option value="Office 2013">Office 2013</option>
                        <option value="Office 2016">Office 2016</option>
                        <option value="Office 2019">Office 2019</option>
                        <option value="Office 2021">Office 2021</option>
                        <option value="Office 365">Office 365</option>
                    </select>
                </div>

                <div x-show="isComputerType(addTypeId) && addOffice !== ''" x-cloak>
                    <label class="text-sm font-medium">Office License Type</label>
                    <select
                        name="specs[office_license]"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        x-model="addOfficeLicense"
                        :disabled="!isComputerType(addTypeId) || addOffice === ''"
                    >
                        <option value="">-- Select License --</option>
                        <option value="Cracked">Cracked</option>
                        <option value="OEM Licensed">OEM Licensed</option>
                    </select>
                </div>

                <div x-show="isComputerType(addTypeId)" x-cloak>
                    <label class="text-sm font-medium">Memory</label>
                    <input
                        name="specs[memory]"
                        value="{{ old('specs.memory') }}"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
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
                        placeholder="Example: 256GB SSD / 1TB HDD"
                        :disabled="!isComputerType(addTypeId)"
                    >
                </div>

                <div x-show="isComputerType(addTypeId)" x-cloak>
                    <label class="text-sm font-medium">Form Factor</label>
                    <input
                        name="specs[form_factor]"
                        value="{{ old('specs.form_factor') }}"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        placeholder="Example: Tower, SFF, Mini PC, All-in-One"
                        :disabled="!isComputerType(addTypeId)"
                    >
                </div>

                <div>
                    <label class="text-sm font-medium">Unit Price</label>
                    <input
                        name="unit_price"
                        value="{{ old('unit_price') }}"
                        type="number"
                        step="0.01"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                    >
                </div>

                <div>
                    <label class="text-sm font-medium">Date Acquired</label>
                    <input
                        name="date_acquired"
                        value="{{ old('date_acquired') }}"
                        type="date"
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
                    placeholder="Example: Initial check, cleaned, inspected"
                >{{ old('maintenance_remarks') }}</textarea>
            </div>

            <div>
                <label class="text-sm font-medium">Notes</label>
                <textarea
                    name="notes"
                    rows="3"
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
        <form method="POST" :action="`{{ url('/admin/devices') }}/${editDevice.id}`" class="space-y-4">
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
                    >
                </div>

                <div>
                    <label class="text-sm font-medium">Serial Number</label>
                    <input
                        name="serial_number"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        x-model="editDevice.serial_number"
                        placeholder="Enter serial number"
                    >
                </div>

                <div>
                    <label class="text-sm font-medium">Brand</label>
                    <input
                        name="brand"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        x-model="editDevice.brand"
                    >
                </div>

                <div>
                    <label class="text-sm font-medium">Model</label>
                    <input
                        name="model"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        x-model="editDevice.model"
                        placeholder="Example: Epson L3110, Acer Aspire"
                    >
                </div>

                <div x-show="isComputerType(editDevice.device_type_id)" x-cloak>
                    <label class="text-sm font-medium">MAC Address</label>
                    <input
                        name="mac_address"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        x-model="editDevice.mac_address"
                        :disabled="!isComputerType(editDevice.device_type_id)"
                    >
                </div>

                <div x-show="isComputerType(editDevice.device_type_id)" x-cloak>
                    <label class="text-sm font-medium">Operating System</label>
                    <select
                        name="specs[os_version]"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        x-model="editDevice.specs.os_version"
                        :disabled="!isComputerType(editDevice.device_type_id)"
                    >
                        <option value="">-- Select OS --</option>
                        <option value="Windows 7">Windows 7</option>
                        <option value="Windows 8">Windows 8</option>
                        <option value="Windows 10">Windows 10</option>
                        <option value="Windows 11">Windows 11</option>
                    </select>
                </div>

                <div x-show="isComputerType(editDevice.device_type_id) && editDevice.specs.os_version !== ''" x-cloak>
                    <label class="text-sm font-medium">OS License Type</label>
                    <select
                        name="specs[os_license]"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        x-model="editDevice.specs.os_license"
                        :disabled="!isComputerType(editDevice.device_type_id) || editDevice.specs.os_version === ''"
                    >
                        <option value="">-- Select License --</option>
                        <option value="Cracked">Cracked</option>
                        <option value="OEM Licensed">OEM Licensed</option>
                        <option value="Retail Licensed">Retail Licensed</option>
                    </select>
                </div>

                <div x-show="isComputerType(editDevice.device_type_id)" x-cloak>
                    <label class="text-sm font-medium">Microsoft Office Version</label>
                    <select
                        name="specs[office_version]"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        x-model="editDevice.specs.office_version"
                        :disabled="!isComputerType(editDevice.device_type_id)"
                    >
                        <option value="">-- Select Office Version --</option>
                        <option value="Office 2007">Office 2007</option>
                        <option value="Office 2010">Office 2010</option>
                        <option value="Office 2013">Office 2013</option>
                        <option value="Office 2016">Office 2016</option>
                        <option value="Office 2019">Office 2019</option>
                        <option value="Office 2021">Office 2021</option>
                        <option value="Office 365">Office 365</option>
                    </select>
                </div>

                <div x-show="isComputerType(editDevice.device_type_id) && editDevice.specs.office_version !== ''" x-cloak>
                    <label class="text-sm font-medium">Office License Type</label>
                    <select
                        name="specs[office_license]"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        x-model="editDevice.specs.office_license"
                        :disabled="!isComputerType(editDevice.device_type_id) || editDevice.specs.office_version === ''"
                    >
                        <option value="">-- Select License --</option>
                        <option value="Cracked">Cracked</option>
                        <option value="OEM Licensed">OEM Licensed</option>
                    </select>
                </div>

                <div x-show="isComputerType(editDevice.device_type_id)" x-cloak>
                    <label class="text-sm font-medium">Memory</label>
                    <input
                        name="specs[memory]"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        x-model="editDevice.specs.memory"
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
                        placeholder="Example: 256GB SSD / 1TB HDD"
                        :disabled="!isComputerType(editDevice.device_type_id)"
                    >
                </div>

                <div x-show="isComputerType(editDevice.device_type_id)" x-cloak>
                    <label class="text-sm font-medium">Form Factor</label>
                    <input
                        name="specs[form_factor]"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        x-model="editDevice.specs.form_factor"
                        placeholder="Example: Tower, SFF, Mini PC, All-in-One"
                        :disabled="!isComputerType(editDevice.device_type_id)"
                    >
                </div>

                <div>
                    <label class="text-sm font-medium">Unit Price</label>
                    <input
                        name="unit_price"
                        type="number"
                        step="0.01"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        x-model="editDevice.unit_price"
                    >
                </div>

                <div>
                    <label class="text-sm font-medium">Date Acquired</label>
                    <input
                        name="date_acquired"
                        type="date"
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
                    class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                    x-model="editDevice.maintenance_remarks"
                ></textarea>
            </div>

            <div>
                <label class="text-sm font-medium">Notes</label>
                <textarea
                    name="notes"
                    rows="3"
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

            <form method="POST" :action="`{{ url('/admin/devices') }}/${deleteDeviceId}`" class="flex gap-2">
                @csrf
                @method('DELETE')

                <button
                    type="submit"
                    class="rounded-lg bg-red-600 px-4 py-2 text-white hover:bg-red-700"
                >
                    Yes, Delete
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
@endsection
