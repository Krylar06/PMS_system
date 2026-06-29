<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\QuickUpdateDeviceRequest;
use App\Models\Device;
use App\Models\DeviceMaintenanceRecord;
use App\Services\DeviceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeviceMaintenanceController extends Controller
{
    public function __construct(protected DeviceService $deviceService)
    {
    }

    /**
     * Quick update popup used on the Issued Devices page.
     */
    public function quickUpdate(QuickUpdateDeviceRequest $request, Device $device)
    {
        $data = $request->validated();

        $data['device_type_id'] = $data['device_type_id'] ?? $device->device_type_id;
        $data['condition']      = $data['condition']      ?? $device->condition ?? 'serviceable';

        if (! array_key_exists('status', $data)) {
            unset($data['status']);
        }

        $data = $this->deviceService->cleanByType($data);

        $device->update($data);

        return back()->with('success', 'Device updated.');
    }

    /**
     * Mark the device as checked / maintained and log a history record.
     */
    public function markChecked(Request $request, Device $device)
    {
        $data = $request->validate([
            'maintenance_date' => ['nullable', 'date'],
            'maintenance_type' => ['nullable', 'string', 'max:255'],
            'remarks'          => ['nullable', 'string'],
        ]);

        $maintenanceDate = $data['maintenance_date'] ?? now()->toDateString();
        $maintenanceType = $data['maintenance_type'] ?? 'Checked';
        $remarks         = $data['remarks']          ?? 'Checked/Maintained today';

        DeviceMaintenanceRecord::create([
            'device_id'        => $device->id,
            'maintenance_date' => $maintenanceDate,
            'maintenance_type' => $maintenanceType,
            'remarks'          => $remarks,
            'checked_by'       => Auth::id(),
        ]);

        $device->update([
            'last_maintenance_date' => $maintenanceDate,
            'maintenance_remarks'   => $remarks,
        ]);

        return redirect()
            ->route('admin.devices.show', $device->id)
            ->with('success', 'Device has been marked as checked.');
    }

    /**
     * Show the full maintenance history for a single device.
     */
    public function history(Device $device)
    {
        $device->load([
            'type',
            'maintenanceRecords.checkedBy',
        ]);

        $records = $device->maintenanceRecords()
            ->with('checkedBy')
            ->orderByDesc('maintenance_date')
            ->orderByDesc('id')
            ->get();

        return view('admin.devices.maintenance-history', compact('device', 'records'));
    }
}