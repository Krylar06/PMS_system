<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDeviceRequest;
use App\Http\Requests\UpdateDeviceRequest;
use App\Models\Device;
use App\Services\DeviceService;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function __construct(protected DeviceService $deviceService)
    {
    }

    public function index(Request $request)
    {
        $q         = $request->string('q')->toString();
        $typeId    = $request->integer('type');
        $condition = $request->query('condition');

        if (! in_array($condition, ['serviceable', 'unserviceable'], true)) {
            $condition = null;
        }

        $devices = Device::query()
            ->with([
                'type',
                'currentAssignment.staff',
                'latestMaintenanceRecord',
            ])
            ->when($q, function ($query) use ($q) {
                return $query->where(function ($sub) use ($q) {
                    $sub->where('property_number', 'like', "%{$q}%")
                        ->orWhere('serial_number',  'like', "%{$q}%")
                        ->orWhere('brand',           'like', "%{$q}%")
                        ->orWhere('model',           'like', "%{$q}%")
                        ->orWhere('mac_address',     'like', "%{$q}%");
                });
            })
            ->when($typeId, fn ($query) => $query->where('device_type_id', $typeId))
            ->when($condition, fn ($query) => $query->where('condition', $condition))
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $types = $this->deviceService->allowedTypes();

        return view('admin.devices.index', compact('devices', 'q', 'typeId', 'condition', 'types'));
    }

    public function create()
    {
        $types = $this->deviceService->allowedTypes();

        return view('admin.devices.create', compact('types'));
    }

    public function store(StoreDeviceRequest $request)
    {
        $data = $request->validated();

        $data['status']    = 'available';
        $data['condition'] = $data['condition'] ?? 'serviceable';
        $data              = $this->deviceService->cleanByType($data);

        Device::create($data);

        return redirect()->back()->with('success', 'Device added successfully.');
    }

    public function show(Device $device)
    {
        $device->load([
            'type',
            'currentAssignment.staff.office.college',
            'latestMaintenanceRecord',
        ]);

        $types = $this->deviceService->allowedTypes();

        return view('admin.devices.show', compact('device', 'types'));
    }

    public function edit(Device $device)
    {
        $device->load('type');

        $types = $this->deviceService->allowedTypes();

        return view('admin.devices.edit', compact('device', 'types'));
    }

    public function update(UpdateDeviceRequest $request, Device $device)
    {
        $data = $request->validated();

        if (! array_key_exists('status', $data)) {
            unset($data['status']);
        }

        $data['condition'] = $data['condition'] ?? $device->condition ?? 'serviceable';
        $data              = $this->deviceService->cleanByType($data);

        $device->update($data);

        return redirect()->route('admin.devices.index')->with('success', 'Device updated.');
    }

    public function destroy(Device $device)
    {
        $device->delete();

        return redirect()->route('admin.devices.index')->with('success', 'Device deleted.');
    }
}