<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Device;
use App\Models\DeviceMaintenanceRecord;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class DeviceChecklistController extends Controller
{
    public function create(Device $device)
    {
        $device->load([
            'type',
            'currentAssignment.staff.office.college',
        ]);

        return view('admin.devices.checklist-form', [
            'device' => $device,
            'checklistItems' => $this->checklistItems(),
            'softwareItems' => $this->softwareItems(),
            'defaultDate' => now()->toDateString(),
        ]);
    }

    public function generate(Request $request, Device $device)
    {
        $statusValues = ['OK', 'Not OK'];
        $softwareValues = ['check', 'dash'];

        $data = $request->validate([
            'maintenance_date' => ['required', 'date', 'before_or_equal:today'],

            'system_unit' => ['nullable', Rule::in($statusValues)],
            'monitor' => ['nullable', Rule::in($statusValues)],
            'keyboard' => ['nullable', Rule::in($statusValues)],
            'mouse' => ['nullable', Rule::in($statusValues)],
            'avr_ups' => ['nullable', Rule::in($statusValues)],
            'printer' => ['nullable', Rule::in($statusValues)],

            'software_anti_virus' => ['nullable', Rule::in($softwareValues)],
            'software_scan_remove' => ['nullable', Rule::in($softwareValues)],

            'remarks' => ['nullable', 'string', 'max:1000'],
            'corrective_action' => ['nullable', 'string', 'max:1000'],
        ], [
            'maintenance_date.before_or_equal' => 'The checklist date cannot be in the future.',
        ]);

        $device->load([
            'type',
            'currentAssignment.staff.office.college',
        ]);

        $checklistItems = $this->checklistItems();
        $softwareItems = $this->softwareItems();
        $checklistValues = [];

        foreach ($checklistItems as $key => $label) {
            $checklistValues[$key] = $data[$key] ?? '';
        }

        $softwareValuesForPdf = [
            'software_anti_virus' => $data['software_anti_virus'] ?? '',
            'software_scan_remove' => $data['software_scan_remove'] ?? '',
        ];

        $remarks = trim((string) ($data['remarks'] ?? ''));
        $correctiveAction = trim((string) ($data['corrective_action'] ?? ''));

        $recordRemarks = $this->buildMaintenanceRemarks(
            $checklistItems,
            $checklistValues,
            $softwareItems,
            $softwareValuesForPdf,
            $remarks,
            $correctiveAction
        );

        DeviceMaintenanceRecord::create([
            'device_id' => $device->id,
            'maintenance_date' => $data['maintenance_date'],
            'maintenance_type' => 'Preventive Maintenance Checklist',
            'remarks' => $recordRemarks,
            'checked_by' => Auth::id(),
        ]);

        $device->update([
            'last_maintenance_date' => $data['maintenance_date'],
            'maintenance_remarks' => $remarks ?: 'Preventive maintenance checklist completed.',
        ]);

        ActivityLog::record('updated', "Generated preventive maintenance checklist for device \"{$device->property_number}\"", $device);

        $pdf = Pdf::loadView('admin.devices.checklist-pdf', [
            'device' => $device,
            'checklistItems' => $checklistItems,
            'checklistValues' => $checklistValues,
            'softwareItems' => $softwareItems,
            'softwareValues' => $softwareValuesForPdf,
            'maintenanceDate' => $data['maintenance_date'],
            'remarks' => $remarks,
            'correctiveAction' => $correctiveAction,
            'checkedBy' => Auth::user(),
        ])->setPaper('a4', 'landscape');

        $safePropertyNumber = str($device->property_number ?? 'device')
            ->replace(['/', '\\', ' '], '-')
            ->toString();

        return $pdf->stream('preventive-maintenance-checklist-' . $safePropertyNumber . '-' . $data['maintenance_date'] . '.pdf');
    }

    private function checklistItems(): array
    {
        return [
            'system_unit' => 'System Unit - Check for power on',
            'monitor' => 'Monitor - Check display',
            'keyboard' => 'Keyboard - Check keys',
            'mouse' => 'Mouse - Check mouse left/right buttons',
            'avr_ups' => 'AVR/UPS - Check for power recovery',
            'printer' => 'Printer - Check printout',
        ];
    }

    private function softwareItems(): array
    {
        return [
            'software_anti_virus' => 'Setup Anti-Virus',
            'software_scan_remove' => 'System Scan and Removal of Malicious Software',
        ];
    }

    private function buildMaintenanceRemarks(
        array $checklistItems,
        array $checklistValues,
        array $softwareItems,
        array $softwareValues,
        string $remarks,
        string $correctiveAction
    ): string {
        $lines = ['Preventive maintenance checklist generated.'];

        foreach ($checklistItems as $key => $label) {
            $lines[] = $label . ': ' . ($checklistValues[$key] ?: '-');
        }

        foreach ($softwareItems as $key => $label) {
            $value = ($softwareValues[$key] ?? '') === 'check' ? '✓' : '-';
            $lines[] = $label . ': ' . $value;
        }

        if ($remarks !== '') {
            $lines[] = 'Remarks: ' . $remarks;
        }

        if ($correctiveAction !== '') {
            $lines[] = 'Corrective Action: ' . $correctiveAction;
        }

        return implode("\n", $lines);
    }
}
