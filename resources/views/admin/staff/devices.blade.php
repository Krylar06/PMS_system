@extends('admin.layouts.app')

@section('title', 'Issued Devices')
@section('page_title', 'Issued Devices')

@section('content')
<div
    x-data="{
        issueOpen: {{ $errors->has('device_id') ? 'true' : 'false' }},
        editOpen: false,
        deleteOpen: false,

        deviceSearch: '',
        selectedDevice: null,

        availableDevices: @js(
            $availableDevices->map(function ($device) {
                return [
                    'id' => $device->id,
                    'type' => $device->type?->name ?? 'Device',
                    'property_number' => $device->property_number,
                    'brand' => $device->brand,
                    'model' => $device->model,
                    'status' => $device->status,
                ];
            })->values()
        ),

        editDevice: {
            id: null,
            device_type_id: '',
            type_name: '',
            property_number: '',
            brand: '',
            model: '',
            mac_address: '',
            unit_price: '',
            date_acquired: '',
            last_maintenance_date: '',
            maintenance_remarks: '',
            status: '',
            notes: '',
            specs: {}
        },

        deleteDeviceId: null,

        filteredDevices() {
            let search = this.deviceSearch.toLowerCase().trim();

            let devices = this.availableDevices.filter(device => {
                return device.status === 'available';
            });

            if (!search) {
                return devices.slice(0, 8);
            }

            return devices.filter(device => {
                return [
                    device.type,
                    device.property_number,
                    device.brand,
                    device.model
                ].filter(Boolean).join(' ').toLowerCase().includes(search);
            }).slice(0, 10);
        },

        selectDevice(device) {
            this.selectedDevice = device;
            this.deviceSearch = `${device.type} - ${device.property_number} - ${device.brand ?? ''} ${device.model ?? ''}`.trim();
        },

        clearSelectedDevice() {
            this.selectedDevice = null;
            this.deviceSearch = '';
        },

        openIssueModal() {
            this.issueOpen = true;
            this.deviceSearch = '';
            this.selectedDevice = null;
        },

        isComputerType(typeName) {
            return ['desktop', 'laptop'].includes(String(typeName || '').toLowerCase());
        },

        openEdit(device) {
            device.specs = device.specs ?? {};
            device.specs.os = device.specs.os ?? '';

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
    {{-- Breadcrumb --}}
    <div class="text-sm text-gray-500 leading-6 break-words">
        <a class="text-blue-600 hover:underline" href="{{ route('admin.colleges.index') }}">
            Colleges
        </a>

        <span class="mx-1">/</span>

        <a class="text-blue-600 hover:underline" href="{{ route('admin.offices.index', $staff->office->college) }}">
            {{ $staff->office->college->name }}
        </a>

        <span class="mx-1">/</span>

        <a class="text-blue-600 hover:underline" href="{{ route('admin.staff.index', $staff->office) }}">
            {{ $staff->office->name }}
        </a>

        <span class="mx-1">/</span>

        <span class="font-medium text-gray-700">
            {{ $staff->last_name }}, {{ $staff->first_name }}
        </span>

        <span class="mx-1">/</span>

        <span>Issued Devices</span>
    </div>

    {{-- Header --}}
    <div class="flex items-start justify-between gap-3">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">
                Issued Devices
            </h1>
        </div>

        <button
            type="button"
            class="shrink-0 inline-flex items-center rounded-xl bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700"
            @click="openIssueModal()"
        >
            + Issue Device
        </button>
    </div>

    {{-- Alerts --}}
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

    @if(session('info'))
        <div class="rounded-xl bg-blue-100 px-4 py-3 text-sm text-blue-700">
            {{ session('info') }}
        </div>
    @endif

    {{-- Mobile cards --}}
    <div class="grid grid-cols-1 gap-3 md:hidden">
        @forelse($issued as $assignment)
            @php
                $dev = $assignment->device;
                $typeName = $dev?->type?->name ?? 'Device';
                $isComputer = in_array(strtolower($typeName), ['desktop', 'laptop']);
            @endphp

            @if($dev)
                <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                    <div class="space-y-3">
                        <div>
                            <div class="text-xs font-medium uppercase text-gray-500">
                                {{ $typeName }}
                            </div>

                            <a
                                href="{{ route('admin.devices.show', $dev) }}"
                                class="mt-1 block text-base font-semibold text-blue-600 hover:underline"
                            >
                                {{ $dev->property_number }}
                            </a>

                            <div class="mt-1 text-sm text-gray-600">
                                {{ $dev->brand ?: '-' }}
                                @if($dev->model)
                                    • {{ $dev->model }}
                                @endif
                            </div>
                        </div>

                        @if($isComputer && $dev->mac_address)
                            <div>
                                <div class="text-xs text-gray-500">MAC Address</div>
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $dev->mac_address }}
                                </div>
                            </div>
                        @endif

                        <div>
                            <div class="text-xs text-gray-500">Issued At</div>
                            <div class="text-sm font-medium text-gray-900">
                                {{ $assignment->issued_at ? $assignment->issued_at->format('Y-m-d H:i') : '-' }}
                            </div>
                        </div>

                        <div>
                            <div class="text-xs text-gray-500">Last Maintenance</div>

                            @if($dev->last_maintenance_date)
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $dev->last_maintenance_date->format('M d, Y') }}
                                </div>

                                @if($dev->maintenance_remarks)
                                    <div class="text-xs text-gray-500">
                                        {{ $dev->maintenance_remarks }}
                                    </div>
                                @endif
                            @else
                                <div class="text-sm text-gray-400">
                                    Not yet checked
                                </div>
                            @endif
                        </div>

                        <div class="flex flex-wrap gap-2 pt-2">
                            <form method="POST" action="{{ route('admin.staff.devices.return', [$staff, $assignment]) }}">
                                @csrf

                                <button
                                    type="submit"
                                    onclick="return confirm('Return this device?')"
                                    class="rounded-lg bg-gray-900 px-3 py-1.5 text-sm font-medium text-white hover:bg-black"
                                >
                                    Return
                                </button>
                            </form>

                            <form method="POST" action="{{ route('admin.devices.markChecked', $dev) }}">
                                @csrf
                                @method('PATCH')

                                <button
                                    type="submit"
                                    class="rounded-lg bg-green-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-green-700"
                                >
                                    Mark Checked
                                </button>
                            </form>

                            <a
                                href="{{ route('admin.devices.history', $dev) }}"
                                class="rounded-lg bg-purple-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-purple-700"
                            >
                                History
                            </a>

                            <button
                                type="button"
                                class="rounded-lg bg-blue-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-blue-700"
                                @click="openEdit({
                                    id: {{ $dev->id }},
                                    device_type_id: @js($dev->device_type_id),
                                    type_name: @js($typeName),
                                    property_number: @js($dev->property_number),
                                    brand: @js($dev->brand ?? ''),
                                    model: @js($dev->model ?? ''),
                                    mac_address: @js($dev->mac_address ?? ''),
                                    unit_price: @js($dev->unit_price ?? ''),
                                    date_acquired: @js($dev->date_acquired ? $dev->date_acquired->format('Y-m-d') : ''),
                                    last_maintenance_date: @js($dev->last_maintenance_date ? $dev->last_maintenance_date->format('Y-m-d') : ''),
                                    maintenance_remarks: @js($dev->maintenance_remarks ?? ''),
                                    status: @js($dev->status ?? ''),
                                    notes: @js($dev->notes ?? ''),
                                    specs: @js(['os' => data_get($dev->specs, 'os', '')])
                                })"
                            >
                                Edit
                            </button>

                            <button
                                type="button"
                                class="rounded-lg bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700"
                                @click="openDelete({{ $dev->id }})"
                            >
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        @empty
            <div class="rounded-xl border border-gray-200 bg-white p-6 text-center text-gray-500">
                No issued devices.
            </div>
        @endforelse
    </div>

    {{-- Desktop table --}}
    <div class="hidden overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm md:block">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="border-b border-gray-200 bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 font-semibold text-gray-700">Type</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Property #</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Brand</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">MAC</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Issued At</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Last Maintenance</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">
                    @forelse($issued as $assignment)
                        @php
                            $dev = $assignment->device;
                            $typeName = $dev?->type?->name ?? 'Device';
                            $isComputer = in_array(strtolower($typeName), ['desktop', 'laptop']);
                        @endphp

                        @if($dev)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-gray-900">
                                    {{ $typeName }}
                                </td>

                                <td class="px-4 py-3">
                                    <a
                                        href="{{ route('admin.devices.show', $dev) }}"
                                        class="font-medium text-blue-600 hover:underline"
                                    >
                                        {{ $dev->property_number }}
                                    </a>
                                </td>

                                <td class="px-4 py-3 text-gray-700">
                                    {{ $dev->brand ?: '-' }}
                                </td>

                                <td class="px-4 py-3 text-gray-700">
                                    {{ $isComputer ? ($dev->mac_address ?: '-') : '-' }}
                                </td>

                                <td class="px-4 py-3 text-gray-700">
                                    {{ $assignment->issued_at ? $assignment->issued_at->format('Y-m-d H:i') : '-' }}
                                </td>

                                <td class="px-4 py-3 text-gray-700">
                                    @if($dev->last_maintenance_date)
                                        <div class="font-medium text-gray-900">
                                            {{ $dev->last_maintenance_date->format('M d, Y') }}
                                        </div>

                                        @if($dev->maintenance_remarks)
                                            <div class="max-w-xs truncate text-xs text-gray-500">
                                                {{ $dev->maintenance_remarks }}
                                            </div>
                                        @endif
                                    @else
                                        <span class="text-gray-400">Not yet checked</span>
                                    @endif
                                </td>

                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <form method="POST" action="{{ route('admin.staff.devices.return', [$staff, $assignment]) }}">
                                            @csrf

                                            <button
                                                type="submit"
                                                onclick="return confirm('Return this device?')"
                                                class="rounded-lg bg-gray-900 px-3 py-1.5 text-sm font-medium text-white hover:bg-black"
                                            >
                                                Return
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('admin.devices.markChecked', $dev) }}">
                                            @csrf
                                            @method('PATCH')

                                            <button
                                                type="submit"
                                                class="rounded-lg bg-green-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-green-700"
                                            >
                                                Mark Checked
                                            </button>
                                        </form>

                                        <a
                                            href="{{ route('admin.devices.history', $dev) }}"
                                            class="rounded-lg bg-purple-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-purple-700"
                                        >
                                            History
                                        </a>

                                        <button
                                            type="button"
                                            class="rounded-lg bg-blue-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-blue-700"
                                            @click="openEdit({
                                                id: {{ $dev->id }},
                                                device_type_id: @js($dev->device_type_id),
                                                type_name: @js($typeName),
                                                property_number: @js($dev->property_number),
                                                brand: @js($dev->brand ?? ''),
                                                model: @js($dev->model ?? ''),
                                                mac_address: @js($dev->mac_address ?? ''),
                                                unit_price: @js($dev->unit_price ?? ''),
                                                date_acquired: @js($dev->date_acquired ? $dev->date_acquired->format('Y-m-d') : ''),
                                                last_maintenance_date: @js($dev->last_maintenance_date ? $dev->last_maintenance_date->format('Y-m-d') : ''),
                                                maintenance_remarks: @js($dev->maintenance_remarks ?? ''),
                                                status: @js($dev->status ?? ''),
                                                notes: @js($dev->notes ?? ''),
                                                specs: @js(['os' => data_get($dev->specs, 'os', '')])
                                            })"
                                        >
                                            Edit
                                        </button>

                                        <button
                                            type="button"
                                            class="rounded-lg bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700"
                                            @click="openDelete({{ $dev->id }})"
                                        >
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                No issued devices.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ISSUE MODAL --}}
    <x-modal show="issueOpen" title="Issue a device to {{ $staff->first_name }}">
        <form method="POST" action="{{ route('admin.staff.devices.issue', $staff) }}" class="space-y-4">
            @csrf

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">
                    Available Devices
                </label>

                <input
                    type="text"
                    x-model="deviceSearch"
                    @input="selectedDevice = null"
                    placeholder="Search device type, property number, brand, or model..."
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >

                <input
                    type="hidden"
                    name="device_id"
                    :value="selectedDevice ? selectedDevice.id : ''"
                >

                @error('device_id')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div
                x-show="!selectedDevice"
                class="max-h-64 overflow-y-auto rounded-lg border border-gray-200"
            >
                <template x-if="filteredDevices().length === 0">
                    <div class="px-4 py-3 text-sm text-gray-500">
                        No available devices found.
                    </div>
                </template>

                <template x-for="device in filteredDevices()" :key="device.id">
                    <button
                        type="button"
                        @click="selectDevice(device)"
                        class="block w-full border-b border-gray-100 px-4 py-3 text-left hover:bg-blue-50"
                    >
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <div class="font-semibold text-gray-900">
                                    <span x-text="device.property_number"></span>
                                </div>

                                <div class="mt-1 text-sm text-gray-600">
                                    <span x-text="device.type"></span>
                                    <span> • </span>
                                    <span x-text="device.brand || 'No brand'"></span>

                                    <template x-if="device.model">
                                        <span>
                                            • <span x-text="device.model"></span>
                                        </span>
                                    </template>
                                </div>
                            </div>

                            <span class="rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-700">
                                Available
                            </span>
                        </div>
                    </button>
                </template>
            </div>

            <div
                x-show="selectedDevice"
                x-cloak
                class="rounded-lg border border-green-200 bg-green-50 p-4"
            >
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="text-sm font-medium text-green-700">
                            Selected Device
                        </div>

                        <div class="mt-1 font-semibold text-gray-900">
                            <span x-text="selectedDevice?.property_number"></span>
                        </div>

                        <div class="mt-1 text-sm text-gray-600">
                            <span x-text="selectedDevice?.type"></span>
                            <span> • </span>
                            <span x-text="selectedDevice?.brand || 'No brand'"></span>

                            <template x-if="selectedDevice?.model">
                                <span>
                                    • <span x-text="selectedDevice?.model"></span>
                                </span>
                            </template>
                        </div>
                    </div>

                    <button
                        type="button"
                        @click="clearSelectedDevice()"
                        class="text-sm font-medium text-red-600 hover:text-red-700"
                    >
                        Remove
                    </button>
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button
                    type="button"
                    class="rounded-lg bg-gray-100 px-4 py-2 text-gray-700 hover:bg-gray-200"
                    @click="issueOpen = false"
                >
                    Cancel
                </button>

                <button
                    type="submit"
                    :disabled="!selectedDevice"
                    :class="!selectedDevice ? 'cursor-not-allowed bg-blue-300' : 'bg-blue-600 hover:bg-blue-700'"
                    class="rounded-lg px-4 py-2 text-white"
                >
                    Issue Device
                </button>
            </div>
        </form>
    </x-modal>

    {{-- EDIT MODAL --}}
    <x-modal show="editOpen" title="Edit Device">
        <form method="POST" :action="`{{ url('/admin/devices') }}/${editDevice.id}`" class="space-y-4">
            @csrf
            @method('PUT')

            <input type="hidden" name="device_type_id" x-model="editDevice.device_type_id">

            <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                <div>
                    <label class="text-sm font-medium">Device Type</label>
                    <input
                        type="text"
                        class="mt-1 w-full rounded-lg border border-gray-300 bg-gray-100 px-3 py-2"
                        x-model="editDevice.type_name"
                        readonly
                    >
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
                    >
                </div>

                <div x-show="isComputerType(editDevice.type_name)" x-cloak>
                    <label class="text-sm font-medium">MAC Address</label>
                    <input
                        name="mac_address"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        x-model="editDevice.mac_address"
                        :disabled="!isComputerType(editDevice.type_name)"
                    >
                </div>

                <div x-show="isComputerType(editDevice.type_name)" x-cloak>
                    <label class="text-sm font-medium">Operating System</label>
                    <input
                        name="specs[os_version]"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        x-model="editDevice.specs.os_version"
                        :disabled="!isComputerType(editDevice.type_name)"
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
                    <label class="text-sm font-medium">Status</label>
                    <select
                        name="status"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        x-model="editDevice.status"
                    >
                        <option value="available">Available</option>
                        <option value="issued">Issued</option>
                        <option value="maintenance">Maintenance</option>
                        <option value="repair">Repair</option>
                        <option value="retired">Retired</option>
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

            <div class="flex justify-end gap-2 pt-2">
                <button
                    type="button"
                    class="rounded-lg bg-gray-100 px-4 py-2 text-gray-700 hover:bg-gray-200"
                    @click="editOpen = false"
                >
                    Cancel
                </button>

                <button class="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                    Save Changes
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

                <button class="rounded-lg bg-red-600 px-4 py-2 text-white hover:bg-red-700">
                    Yes, Delete
                </button>

                <button
                    type="button"
                    class="rounded-lg bg-gray-100 px-4 py-2 text-gray-700 hover:bg-gray-200"
                    @click="deleteOpen = false"
                >
                    Cancel
                </button>
            </form>
        </div>
    </x-modal>
</div>
@endsection
