<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PreventiveMaintenanceReportExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDeviceRequest;
use App\Http\Requests\UpdateDeviceRequest;
use App\Models\Device;
use App\Models\DeviceMaintenanceRecord;
use App\Models\DeviceType;
use App\Models\Office;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class DeviceController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q')->toString();
        $typeId = $request->integer('type');
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
                $query->where(function ($sub) use ($q) {
                    $sub->where('property_number', 'like', "%{$q}%")
                        ->orWhere('serial_number', 'like', "%{$q}%")
                        ->orWhere('brand', 'like', "%{$q}%")
                        ->orWhere('model', 'like', "%{$q}%")
                        ->orWhere('mac_address', 'like', "%{$q}%");
                });
            })
            ->when($typeId, function ($query) use ($typeId) {
                $query->where('device_type_id', $typeId);
            })
            ->when($condition, function ($query) use ($condition) {
                $query->where('condition', $condition);
            })
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $types = $this->allowedDeviceTypes();

        return view('admin.devices.index', compact(
            'devices',
            'q',
            'typeId',
            'condition',
            'types'
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

        Device::create($data);

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
        if (! array_key_exists('status', $data)) {
            unset($data['status']);
        }

        $data['condition'] = $data['condition'] ?? $device->condition ?? 'serviceable';

        $data = $this->cleanDeviceDataByType($data);

        $device->update($data);

        return redirect()
            ->route('admin.devices.index')
            ->with('success', 'Device updated.');
    }

    public function destroy(Device $device)
    {
        $device->delete();

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
                'max:255',
                'unique:devices,property_number,' . $device->id,
            ],

            'serial_number' => ['nullable', 'string', 'max:255'],

            'brand' => ['nullable', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'mac_address' => ['nullable', 'string', 'max:255'],

            'unit_price' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'date_acquired' => ['nullable', 'date'],

            'condition' => ['nullable', 'in:serviceable,unserviceable'],
            'status' => ['nullable', 'in:available,issued,repair,retired'],

            'last_maintenance_date' => ['nullable', 'date'],
            'maintenance_remarks' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],

            'specs' => ['nullable', 'array'],
            'specs.os' => ['nullable', 'string', 'max:255'],
            'specs.memory' => ['nullable', 'string', 'max:255'],
            'specs.storage' => ['nullable', 'string', 'max:255'],
            'specs.form_factor' => ['nullable', 'string', 'max:255'],
        ]);

        /*
        |--------------------------------------------------------------------------
        | If device_type_id is not submitted, use the current device type.
        |--------------------------------------------------------------------------
        */
        $data['device_type_id'] = $data['device_type_id'] ?? $device->device_type_id;
        $data['condition'] = $data['condition'] ?? $device->condition ?? 'serviceable';

        if (! array_key_exists('status', $data)) {
            unset($data['status']);
        }

        $data = $this->cleanDeviceDataByType($data);

        $device->update($data);

        return back()->with('success', 'Device updated.');
    }

    /**
     * Mark the device as checked/maintained today.
     * This also creates a maintenance history record.
     */
    public function markChecked(Request $request, Device $device)
    {
        $data = $request->validate([
            'maintenance_date' => ['nullable', 'date'],
            'maintenance_type' => ['nullable', 'string', 'max:255'],
            'remarks' => ['nullable', 'string'],
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

        return view('admin.devices.generate-qr', compact('devices'));
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

        if (! $isComputerType) {
            $data['mac_address'] = null;

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
                ->filter(fn ($value) => filled($value))
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