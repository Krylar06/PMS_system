<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PreventiveMaintenanceReportExport;
use App\Models\ActivityLog;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDeviceRequest;
use App\Http\Requests\UpdateDeviceRequest;
use App\Models\Device;
use App\Models\DeviceMaintenanceRecord;
use App\Models\College;
use App\Models\DeviceType;
use App\Models\Office;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class DeviceController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q')->toString();
        $typeId = $request->integer('type');
        $collegeId = $request->integer('college');
        $condition = $request->query('condition');

        if (!in_array($condition, ['serviceable', 'unserviceable'], true)) {
            $condition = null;
        }

        $devices = Device::query()
            ->with([
                'type',
                'currentAssignment.staff.office.college',
                'latestMaintenanceRecord',
            ])
            ->when($q, function ($query) use ($q) {
                return $query->where(function ($sub) use ($q) {
                    $sub->where('property_number', 'like', "%{$q}%")
                        ->orWhere('serial_number', 'like', "%{$q}%")
                        ->orWhere('brand', 'like', "%{$q}%")
                        ->orWhere('model', 'like', "%{$q}%")
                        ->orWhere('mac_address', 'like', "%{$q}%");
                });
            })
            ->when($typeId, function ($query) use ($typeId) {
                return $query->where('device_type_id', $typeId);
            })
            ->when($collegeId, function ($query) use ($collegeId) {
                return $query->whereHas('currentAssignment.staff.office', function ($office) use ($collegeId) {
                    $office->where('college_id', $collegeId);
                });
            })
            ->when($condition, function ($query) use ($condition) {
                return $query->where('condition', $condition);
            })
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $types = $this->allowedDeviceTypes();
        $colleges = College::orderBy('name')->get();

        return view('admin.devices.index', compact(
            'devices',
            'q',
            'typeId',
            'collegeId',
            'condition',
            'types',
            'colleges'
        ));
    }

    public function create()
    {
        $types = $this->allowedDeviceTypes();

        return view('admin.devices.create', compact('types'));
    }

    public function store(StoreDeviceRequest $request)
    {
        $data = $request->validated();

        /*
        |--------------------------------------------------------------------------
        | Default Device Availability
        |--------------------------------------------------------------------------
        | Every newly added device is automatically available.
        | Do not let the form decide this.
        */
        $data['status'] = 'available';

        /*
        |--------------------------------------------------------------------------
        | Default Device Condition
        |--------------------------------------------------------------------------
        | Device condition is separate from availability.
        | condition = serviceable / unserviceable
        | status = available / issued / repair / retired
        */
        $data['condition'] = $data['condition'] ?? 'serviceable';

        $data = $this->cleanDeviceDataByType($data);

        $device = Device::create($data);

        ActivityLog::record('created', "Added device \"{$device->property_number}\"", $device);

        return redirect()
            ->back()
            ->with('success', 'Device added successfully.');
    }

    public function show(Device $device)
    {
        $device->load([
            'type',
            'currentAssignment.staff.office.college',
            'latestMaintenanceRecord',
        ]);

        $types = $this->allowedDeviceTypes();

        return view('admin.devices.show', compact('device', 'types'));
    }

    public function edit(Device $device)
    {
        $device->load('type');

        $types = $this->allowedDeviceTypes();

        return view('admin.devices.edit', compact('device', 'types'));
    }

    public function update(UpdateDeviceRequest $request, Device $device)
    {
        $data = $request->validated();

        /*
        |--------------------------------------------------------------------------
        | Keep existing status if not submitted
        |--------------------------------------------------------------------------
        | This prevents accidentally changing issued/available status from forms
        | that do not include a status field.
        */
        if (!array_key_exists('status', $data)) {
            unset($data['status']);
        }

        $data['condition'] = $data['condition'] ?? $device->condition ?? 'serviceable';

        $data = $this->cleanDeviceDataByType($data);

        $device->update($data);

        ActivityLog::record('updated', "Updated device \"{$device->property_number}\"", $device);

        return redirect()
            ->route('admin.devices.index')
            ->with('success', 'Device updated.');
    }

    public function destroy(Device $device)
    {
        $propertyNumber = $device->property_number;
        $device->delete();

        ActivityLog::record('deleted', "Deleted device \"{$propertyNumber}\"");

        return redirect()
            ->route('admin.devices.index')
            ->with('success', 'Device deleted.');
    }

    /**
     * Quick update endpoint used by popup edit on "Issued Devices" page.
     */
    public function quickUpdate(Request $request, Device $device)
    {
        $data = $request->validate([
            'device_type_id' => ['nullable', 'exists:device_types,id'],

            'property_number' => [
                'required',
                'string',
                'max:50',
                'regex:' . StoreDeviceRequest::PROPERTY_NUMBER_REGEX,
                'unique:devices,property_number,' . $device->id,
            ],

            'serial_number' => ['nullable', 'string', 'max:100', 'regex:' . StoreDeviceRequest::SERIAL_NUMBER_REGEX],

            'brand' => ['nullable', 'string', 'max:100', 'regex:' . StoreDeviceRequest::BRAND_MODEL_REGEX],
            'model' => ['nullable', 'string', 'max:100', 'regex:' . StoreDeviceRequest::BRAND_MODEL_REGEX],
            'mac_address' => ['nullable', 'string', 'regex:' . StoreDeviceRequest::MAC_ADDRESS_REGEX],

            'unit_price' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'date_acquired' => ['nullable', 'date', 'before_or_equal:today'],

            'condition' => ['nullable', 'in:serviceable,unserviceable'],
            'status' => ['nullable', 'in:available,issued,repair,retired'],

            'last_maintenance_date' => ['nullable', 'date', 'before_or_equal:today'],
            'maintenance_remarks' => ['nullable', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:2000'],

            'specs' => ['nullable', 'array'],
            'specs.os' => ['nullable', 'string', 'max:100'],
            'specs.memory' => ['nullable', 'string', 'max:50'],
            'specs.storage' => ['nullable', 'string', 'max:50'],
            'specs.form_factor' => ['nullable', 'string', 'max:50'],

            'os_version' => ['nullable', 'string', 'in:Windows 7,Windows 8,Windows 10,Windows 11'],
            'os_license' => ['nullable', 'string', 'in:Cracked,OEM Licensed'],
            'ms_office_version' => ['nullable', 'string', 'in:Office 2007,Office 2010,Office 2013,Office 2016,Office 2019,Office 2021,Microsoft 365'],
            'ms_office_license' => ['nullable', 'string', 'in:Cracked,OEM Licensed'],
        ], [
            'property_number.regex' => 'Property number may only contain letters, numbers, hyphens, and slashes.',
            'serial_number.regex' => 'Serial number may only contain letters, numbers, and hyphens.',
            'brand.regex' => 'Brand may only contain letters and numbers.',
            'model.regex' => 'Model may only contain letters and numbers.',
            'mac_address.regex' => 'Please enter a valid MAC address, e.g. 00:1A:2B:3C:4D:5E.',
            'date_acquired.before_or_equal' => 'Date acquired cannot be in the future.',
            'last_maintenance_date.before_or_equal' => 'Last maintenance date cannot be in the future.',
            'os_version.in' => 'Invalid OS version selected.',
            'os_license.in' => 'OS license must be either Cracked or OEM Licensed.',
            'ms_office_version.in' => 'Invalid MS Office version selected.',
            'ms_office_license.in' => 'MS Office license must be either Cracked or OEM Licensed.',
        ]);

        /*
        |--------------------------------------------------------------------------
        | If device_type_id is not submitted, use the current device type.
        |--------------------------------------------------------------------------
        */
        $data['device_type_id'] = $data['device_type_id'] ?? $device->device_type_id;
        $data['condition'] = $data['condition'] ?? $device->condition ?? 'serviceable';

        if (!array_key_exists('status', $data)) {
            unset($data['status']);
        }

        $data = $this->cleanDeviceDataByType($data);

        $device->update($data);

        ActivityLog::record('updated', "Updated device \"{$device->property_number}\" (quick edit)", $device);

        return back()->with('success', 'Device updated.');
    }

    /**
     * Mark the device as checked/maintained today.
     * This also creates a maintenance history record.
     */
    public function markChecked(Request $request, Device $device)
    {
        $data = $request->validate([
            'maintenance_date' => ['nullable', 'date', 'before_or_equal:today'],
            'maintenance_type' => ['nullable', 'string', 'max:100'],
            'remarks' => ['nullable', 'string', 'max:1000'],
        ], [
            'maintenance_date.before_or_equal' => 'Maintenance date cannot be in the future.',
        ]);

        $maintenanceDate = $data['maintenance_date'] ?? now()->toDateString();
        $maintenanceType = $data['maintenance_type'] ?? 'Checked';
        $remarks = $data['remarks'] ?? 'Checked/Maintained today';

        DeviceMaintenanceRecord::create([
            'device_id' => $device->id,
            'maintenance_date' => $maintenanceDate,
            'maintenance_type' => $maintenanceType,
            'remarks' => $remarks,
            'checked_by' => Auth::id(),
        ]);

        $device->update([
            'last_maintenance_date' => $maintenanceDate,
            'maintenance_remarks' => $remarks,
        ]);

        ActivityLog::record('updated', "Marked device \"{$device->property_number}\" as checked/maintained", $device);

        return redirect()
            ->route('admin.devices.show', $device->id)
            ->with('success', 'Device has been marked as checked.');
    }

    public function maintenanceHistory(Device $device)
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

    public function generateQr()
    {
        $devices = Device::orderBy('property_number')->get();

        $qrCodes = $devices->mapWithKeys(function ($device) {
            $qrPayload = route('admin.devices.show', $device) . '?property_number=' . urlencode($device->property_number);

            return [
                $device->id => QrCode::size(180)->generate($qrPayload),
            ];
        });

        return view('admin.devices.generate-qr', compact('devices', 'qrCodes'));
    }

    public function exportPreventiveMaintenanceReport()
    {
        $filename = 'preventive-maintenance-report-' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download(new PreventiveMaintenanceReportExport, $filename);
    }

    public function exportOfficePreventiveMaintenanceReport(Office $office)
    {
        $safeOfficeName = str($office->name)
            ->lower()
            ->replace(' ', '-')
            ->replace('/', '-');

        $filename = 'preventive-maintenance-report-' . $safeOfficeName . '-' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download(new PreventiveMaintenanceReportExport($office), $filename);
    }

    /**
     * Remove computer-only fields when the device is not Desktop or Laptop.
     */
    private function cleanDeviceDataByType(array $data): array
    {
        $type = DeviceType::find($data['device_type_id'] ?? null);
        $typeName = strtolower($type?->name ?? '');

        $isComputerType = in_array($typeName, ['desktop', 'laptop']);

        if (!$isComputerType) {
            $data['mac_address'] = null;
            $data['os_version'] = null;
            $data['os_license'] = null;
            $data['ms_office_version'] = null;
            $data['ms_office_license'] = null;

            $data['specs'] = collect($data['specs'] ?? [])
                ->except([
                    'os',
                    'memory',
                    'storage',
                    'form_factor',
                ])
                ->toArray();

            if (empty($data['specs'])) {
                $data['specs'] = null;
            }
        }

        if ($isComputerType) {
            $data['specs'] = collect($data['specs'] ?? [])
                ->filter(fn($value) => filled($value))
                ->toArray();

            if (empty($data['specs'])) {
                $data['specs'] = null;
            }
        }

        return $data;
    }

    /**
     * Only show these device types in the Add/Edit dropdown.
     * This does not delete old device types from the database.
     */
    private function allowedDeviceTypes()
    {
        $allowedTypes = [
            'Desktop',
            'Laptop',
            'Printer',
            'Monitor',
            'UPS',
            'AVR',
            'Other',
        ];

        foreach ($allowedTypes as $typeName) {
            DeviceType::firstOrCreate(
                ['name' => $typeName],
                ['slug' => strtolower(str_replace(' ', '-', $typeName))]
            );
        }

        return DeviceType::whereIn('name', $allowedTypes)
            ->get()
            ->sortBy(function ($type) use ($allowedTypes) {
                return array_search($type->name, $allowedTypes);
            })
            ->values();
    }
}