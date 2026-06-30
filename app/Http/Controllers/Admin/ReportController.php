<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\College;
use App\Models\Device;
use App\Models\DeviceMaintenanceRecord;
use App\Models\DeviceType;
use App\Models\Office;
use App\Models\User;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }

    public function assets(Request $request)
    {
        $devicesQuery = $this->filteredAssetsQuery($request);

        $devices = $devicesQuery
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        return view('admin.reports.assets', array_merge([
            'devices' => $devices,
            'selectedTypeId' => $request->integer('type_id'),
            'selectedCollegeId' => $request->integer('college_id'),
            'selectedOfficeId' => $request->integer('office_id'),
            'q' => $request->string('q')->toString(),
        ], $this->filterOptions()));
    }

    public function accounts(Request $request)
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $role = $request->query('role');
        $q = $request->string('q')->toString();

        if (! in_array($role, ['admin', 'custodian'], true)) {
            $role = null;
        }

        $users = User::query()
            ->when($role, fn ($query) => $query->where('role', $role))
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhere('role', 'like', "%{$q}%");
                });
            })
            ->orderBy('role')
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();

        return view('admin.reports.accounts', [
            'users' => $users,
            'role' => $role,
            'q' => $q,
            'adminCount' => User::where('role', 'admin')->count(),
            'custodianCount' => User::where('role', 'custodian')->count(),
        ]);
    }

    public function checkedEquipment(Request $request)
    {
        $adminId = $request->integer('admin_id') ?: null;
        $typeId = $request->integer('type_id') ?: null;
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $q = $request->string('q')->toString();

        $records = DeviceMaintenanceRecord::query()
            ->with([
                'device.type',
                'device.currentAssignment.staff.office.college',
                'checkedBy',
            ])
            ->whereHas('checkedBy', fn ($query) => $query->where('role', 'admin'))
            ->when($adminId, fn ($query) => $query->where('checked_by', $adminId))
            ->when($typeId, function ($query) use ($typeId) {
                $query->whereHas('device', fn ($deviceQuery) => $deviceQuery->where('device_type_id', $typeId));
            })
            ->when($dateFrom, fn ($query) => $query->whereDate('maintenance_date', '>=', $dateFrom))
            ->when($dateTo, fn ($query) => $query->whereDate('maintenance_date', '<=', $dateTo))
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('remarks', 'like', "%{$q}%")
                        ->orWhere('maintenance_type', 'like', "%{$q}%")
                        ->orWhereHas('device', function ($deviceQuery) use ($q) {
                            $deviceQuery->where('property_number', 'like', "%{$q}%")
                                ->orWhere('serial_number', 'like', "%{$q}%")
                                ->orWhere('brand', 'like', "%{$q}%")
                                ->orWhere('model', 'like', "%{$q}%");
                        });
                });
            })
            ->orderByDesc('maintenance_date')
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        $adminSummary = DeviceMaintenanceRecord::query()
            ->selectRaw('checked_by, COUNT(*) as total')
            ->whereHas('checkedBy', fn ($query) => $query->where('role', 'admin'))
            ->with('checkedBy')
            ->groupBy('checked_by')
            ->orderByDesc('total')
            ->get();

        return view('admin.reports.checked-equipment', [
            'records' => $records,
            'adminSummary' => $adminSummary,
            'adminUsers' => User::where('role', 'admin')->orderBy('name')->get(),
            'types' => DeviceType::orderBy('name')->get(),
            'adminId' => $adminId,
            'typeId' => $typeId,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'q' => $q,
        ]);
    }

    public function checklist(Request $request)
    {
        $devices = $this->filteredAssetsQuery($request)
            ->orderBy('property_number')
            ->get();

        return view('admin.reports.checklist', array_merge([
            'devices' => $devices,
            'selectedTypeId' => $request->integer('type_id'),
            'selectedCollegeId' => $request->integer('college_id'),
            'selectedOfficeId' => $request->integer('office_id'),
            'q' => $request->string('q')->toString(),
            'generatedAt' => now(),
        ], $this->filterOptions()));
    }

    private function filteredAssetsQuery(Request $request)
    {
        $typeId = $request->integer('type_id') ?: null;
        $collegeId = $request->integer('college_id') ?: null;
        $officeId = $request->integer('office_id') ?: null;
        $q = $request->string('q')->toString();

        return Device::query()
            ->with([
                'type',
                'currentAssignment.staff.office.college',
                'latestMaintenanceRecord.checkedBy',
            ])
            ->when($typeId, fn ($query) => $query->where('device_type_id', $typeId))
            ->when($collegeId, function ($query) use ($collegeId) {
                $query->whereHas('currentAssignment.staff.office', function ($officeQuery) use ($collegeId) {
                    $officeQuery->where('college_id', $collegeId);
                });
            })
            ->when($officeId, function ($query) use ($officeId) {
                $query->whereHas('currentAssignment.staff', function ($staffQuery) use ($officeId) {
                    $staffQuery->where('office_id', $officeId);
                });
            })
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('property_number', 'like', "%{$q}%")
                        ->orWhere('serial_number', 'like', "%{$q}%")
                        ->orWhere('brand', 'like', "%{$q}%")
                        ->orWhere('model', 'like', "%{$q}%")
                        ->orWhere('computer_name', 'like', "%{$q}%")
                        ->orWhere('mac_address', 'like', "%{$q}%");
                });
            });
    }

    private function filterOptions(): array
    {
        return [
            'types' => DeviceType::orderBy('name')->get(),
            'colleges' => College::orderBy('name')->get(),
            'offices' => Office::with('college')->orderBy('name')->get(),
        ];
    }
}
