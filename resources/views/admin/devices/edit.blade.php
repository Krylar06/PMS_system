@extends('admin.layouts.app')

@section('title', 'Edit Device')
@section('page_title', 'Edit Device')

@section('breadcrumb')
    <a class="text-blue-700 hover:underline" href="{{ route('admin.devices.index') }}">Device Manager</a>
    <span class="mx-2">/</span>
    <span>Edit Device</span>
@endsection

@section('content')
<div class="bg-white rounded shadow-sm p-6 max-w-4xl">
    <form method="POST" action="{{ route('admin.devices.update', $device) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Device Type --}}
            <div>
                <label class="text-sm font-medium">Device Type</label>
                <select name="device_type_id"
                        class="mt-1 w-full border rounded px-3 py-2"
                        required>
                    @foreach($types as $t)
                        <option value="{{ $t->id }}"
                            @selected(old('device_type_id', $device->device_type_id) == $t->id)>
                            {{ $t->name }}
                        </option>
                    @endforeach
                </select>
                @error('device_type_id')
                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                @enderror
            </div>

            {{-- Property Number --}}
            <div>
                <label class="text-sm font-medium">Property Number</label>
                <input name="property_number"
                       value="{{ old('property_number', $device->property_number) }}"
                       class="mt-1 w-full border rounded px-3 py-2"
                       required
                       maxlength="50"
                       pattern="[A-Za-z0-9][A-Za-z0-9\-\/]*"
                       title="Letters, numbers, hyphens, and slashes only">
                @error('property_number')
                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                @enderror
            </div>

            {{-- Brand --}}
            <div>
                <label class="text-sm font-medium">Brand</label>
                <input name="brand"
                       maxlength="100"
                       pattern="[A-Za-zÑñ0-9][A-Za-zÑñ0-9.\-\s]*"
                       title="Letters and numbers only"
                       value="{{ old('brand', $device->brand) }}"
                       class="mt-1 w-full border rounded px-3 py-2">
                @error('brand')
                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                @enderror
            </div>

            {{-- Unit Price --}}
            <div>
                <label class="text-sm font-medium">Unit Price</label>
                <input name="unit_price"
                       type="number"
                       step="0.01"
                       min="0"
                       max="9999999999.99"
                       value="{{ old('unit_price', $device->unit_price) }}"
                       class="mt-1 w-full border rounded px-3 py-2">
                @error('unit_price')
                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                @enderror
            </div>

            {{-- MAC Address --}}
            <div>
                <label class="text-sm font-medium">MAC Address</label>
                <input name="mac_address"
                       value="{{ old('mac_address', $device->mac_address) }}"
                       class="mt-1 w-full border rounded px-3 py-2"
                       maxlength="17" pattern="[0-9A-Fa-f]{2}(:[0-9A-Fa-f]{2}){5}" title="Format: 00:1A:2B:3C:4D:5E" placeholder="00:1A:2B:3C:4D:5E">
                @error('mac_address')
                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                @enderror
            </div>

            {{-- Date Acquired --}}
            <div>
                <label class="text-sm font-medium">Date Acquired</label>
                <input type="date"
                       max="{{ now()->format('Y-m-d') }}"
                       name="date_acquired"
                       value="{{ old('date_acquired', $device->date_acquired) }}"
                       class="mt-1 w-full border rounded px-3 py-2">
                @error('date_acquired')
                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                @enderror
            </div>

            {{-- Status --}}
            <div>
                <label class="text-sm font-medium">Status</label>
                @php $status = old('status', $device->status); @endphp
                <select name="status" class="mt-1 w-full border rounded px-3 py-2" required>
                    <option value="available" @selected($status === 'available')>Available</option>
                    <option value="issued" @selected($status === 'issued')>Issued</option>
                    <option value="repair" @selected($status === 'repair')>Repair</option>
                    <option value="retired" @selected($status === 'retired')>Retired</option>
                </select>
                @error('status')
                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Notes --}}
        <div>
            <label class="text-sm font-medium">Notes</label>
            <textarea name="notes" rows="4" maxlength="2000"
                      class="mt-1 w-full border rounded px-3 py-2">{{ old('notes', $device->notes) }}</textarea>
            @error('notes')
                <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="flex gap-2">
            <button class="px-4 py-2 rounded bg-blue-600 text-white">Update</button>
            <a href="{{ route('admin.devices.index') }}" class="px-4 py-2 rounded bg-gray-100">Cancel</a>
        </div>
    </form>
</div>
@endsection