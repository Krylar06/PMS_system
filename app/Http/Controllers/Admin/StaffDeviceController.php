<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Staff;
use App\Models\Device;
use App\Models\DeviceAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffDeviceController extends Controller
{
    public function index(Staff $staff)
    {
        $staff->load('office.college');

        $issued = DeviceAssignment::query()
            ->where('staff_id', $staff->id)
            ->whereNull('returned_at')
            ->with([
                'device.type',
                'device.latestMaintenanceRecord',
            ])
            ->orderByDesc('issued_at')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Available Devices for Picker
        |--------------------------------------------------------------------------
        | Only show devices that:
        | 1. Have status = available
        | 2. Do not have an active assignment
        */
        $availableDevices = Device::query()
            ->with('type')
            ->where('status', 'available')
            ->whereDoesntHave('currentAssignment')
            ->orderBy('property_number')
            ->get();

        return view('admin.staff.devices', compact('staff', 'issued', 'availableDevices'));
    }

    public function issue(Request $request, Staff $staff)
    {
        $data = $request->validate([
            'device_id' => ['required', 'exists:devices,id'],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Re-check Availability
        |--------------------------------------------------------------------------
        | This prevents issuing devices that were already issued by another admin,
        | even if they were visible in the modal before the page refreshed.
        */
        $device = Device::query()
            ->where('id', $data['device_id'])
            ->where('status', 'available')
            ->whereDoesntHave('currentAssignment')
            ->first();

        if (! $device) {
            return back()
                ->withErrors([
                    'device_id' => 'This device is not available or has already been issued.',
                ])
                ->withInput();
        }

        DeviceAssignment::create([
            'device_id' => $device->id,
            'staff_id' => $staff->id,
            'issued_by' => Auth::id(),
            'issued_at' => now(),
        ]);

        $device->update([
            'status' => 'issued',
        ]);

        ActivityLog::record('issued', "Issued device \"{$device->property_number}\" to {$staff->first_name} {$staff->last_name}", $device);

        return back()->with('success', 'Device issued successfully.');
    }

    public function return(Staff $staff, DeviceAssignment $assignment)
    {
        abort_unless($assignment->staff_id === $staff->id, 404);

        if ($assignment->returned_at) {
            return back()->with('info', 'Device is already returned.');
        }

        $assignment->load('device');

        $assignment->update([
            'returned_at' => now(),
        ]);

        if ($assignment->device) {
            $assignment->device->update([
                'status' => 'available',
            ]);

            ActivityLog::record('returned', "Returned device \"{$assignment->device->property_number}\" from {$staff->first_name} {$staff->last_name}", $assignment->device);
        }

        return back()->with('success', 'Device returned successfully.');
    }
}