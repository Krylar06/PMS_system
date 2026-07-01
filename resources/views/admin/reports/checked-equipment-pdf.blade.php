<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Preventive Maintenance Checklist</title>

    <style>
        @page {
            size: legal landscape;
            margin: 8mm;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 8px;
            color: #111827;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 3px;
        }

        .header-table td {
            border: none;
            vertical-align: top;
            padding: 0;
        }

        .logo {
            width: 48px;
            height: 48px;
        }

        .school-text {
            font-size: 8px;
            line-height: 1.2;
        }

        .school-name {
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
        }

        .unit-name {
            margin-top: 3px;
            font-size: 9px;
        }

        .title {
            text-align: center;
            font-weight: bold;
            font-size: 11px;
            letter-spacing: 0.5px;
            margin-top: 3px;
        }

        .blue-line {
            border-top: 2px solid #1d70b8;
            margin: 3px 0 6px 0;
        }

        .top-fields {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
        }

        .top-fields td {
            border: none;
            padding: 1px 2px;
            font-size: 8px;
        }

        .line {
            display: inline-block;
            border-bottom: 1px solid #111827;
            height: 10px;
            vertical-align: bottom;
        }

        .line-short {
            width: 80px;
        }

        .line-medium {
            width: 170px;
        }

        .main-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .main-table th,
        .main-table td {
            border: 1px solid #111827;
            padding: 3px 2px;
            text-align: center;
            vertical-align: middle;
            word-wrap: break-word;
        }

        .main-table th {
            font-weight: bold;
            background: #f3f4f6;
        }

        .left {
            text-align: left !important;
        }

        .row-height td {
            height: 22px;
        }

        .check {
            font-size: 10px;
            font-weight: bold;
        }

        .signature-table {
            width: 100%;
            margin-top: 14px;
            border-collapse: collapse;
        }

        .signature-table td {
            border: none;
            width: 50%;
            font-size: 9px;
            vertical-align: top;
            padding: 0 20px;
        }

        .sig-line {
            display: inline-block;
            width: 180px;
            border-bottom: 1px solid #111827;
            height: 13px;
            vertical-align: bottom;
        }

        .sig-date-line {
            display: inline-block;
            width: 95px;
            border-bottom: 1px solid #111827;
            height: 13px;
            vertical-align: bottom;
        }
    </style>
</head>

<body>
@php
    if (! isset($records)) {
        $records = isset($record) && $record
            ? collect([$record])
            : collect();
    } elseif (is_array($records)) {
        $records = collect($records);
    }

    $firstRecord = $records->first() ?? null;
    $firstDevice = $firstRecord?->device;

    $firstAssignment = $firstDevice?->currentAssignment;
    $firstStaff = $firstAssignment?->staff;
    $firstOffice = $firstStaff?->office;
    $firstCollege = $firstOffice?->college;

    /*
        Header values:
        - Main unit line uses assigned college/office.
        - Office/Unit uses college code like CICT if available.
    */
    $headerCollegeOffice = $firstCollege?->name
        ?? $firstOffice?->name
        ?? 'Assigned Office / College';

    $officeUnitCode = $firstCollege?->code
        ?? $firstOffice?->name
        ?? '-';

    $reportDate = request('date_to')
        ?? request('date_from')
        ?? now()->toDateString();

    $dateText = \Carbon\Carbon::parse($reportDate)->format('m/d/Y');

    $logoPath = public_path('images/catsu-logo.png');

    $hardwareItems = $checklistItems ?? [
        'system_unit_power_on' => ['group' => 'System Unit', 'label' => 'Check for power on'],
        'monitor_display' => ['group' => 'Monitor', 'label' => 'Check display'],
        'keyboard_keys' => ['group' => 'Keyboard', 'label' => 'Check for keys'],
        'mouse_buttons' => ['group' => 'Mouse', 'label' => 'Check mouse left/right buttons'],
        'avr_ups_power_recovery' => ['group' => 'AVR/UPS', 'label' => 'Check for power recovery'],
        'printer_printout' => ['group' => 'Printer', 'label' => 'Check printout'],
    ];

    $softwareItems = $softwareItems ?? [
        'setup_antivirus' => 'Setup Anti-Virus',
        'system_scan_removal' => 'System Scan and Removal of Malicious Software',
    ];

    $getChecklistData = function ($record) {
        $data = $record->checklist_data ?? [];

        if (is_string($data)) {
            $decoded = json_decode($data, true);
            $data = is_array($decoded) ? $decoded : [];
        }

        return is_array($data) ? $data : [];
    };

    $getDeviceOffice = function ($device) {
        return $device?->currentAssignment?->staff?->office;
    };

    $getDeviceCollege = function ($device) {
        return $device?->currentAssignment?->staff?->office?->college;
    };

    $staffName = function ($staff) {
        if (! $staff) {
            return '-';
        }

        return trim(($staff->last_name ?? '') . ', ' . ($staff->first_name ?? '')) ?: ($staff->name ?? '-');
    };
@endphp

<table class="header-table">
    <tr>
        <td style="width: 55px;">
            @if(file_exists($logoPath))
                <img src="{{ $logoPath }}" class="logo">
            @endif
        </td>

        <td class="school-text">
            <div>Republic of the Philippines</div>
            <div class="school-name">CATANDUANES STATE UNIVERSITY</div>
            <div>Virac, Catanduanes</div>
            <div class="unit-name">{{ $headerCollegeOffice }}</div>
        </td>

        <td style="width: 160px; text-align: right; padding-top: 34px;">
            Date:
            <span class="line line-short">{{ $dateText }}</span>
        </td>
    </tr>
</table>

<div class="blue-line"></div>

<div class="title">PREVENTIVE MAINTENANCE CHECKLIST</div>

<table class="top-fields">
    <tr>
        <td style="width: 55%;">
            Office/Unit:
            <span class="line line-medium">{{ $officeUnitCode }}</span>
        </td>

        <td style="width: 45%;">
            College/Office:
            <span class="line line-medium">{{ $headerCollegeOffice }}</span>
        </td>
    </tr>
</table>

<table class="main-table">
    <thead>
        <tr>
            <th rowspan="2" style="width: 5%;">No.</th>
            <th rowspan="2" style="width: 9%;">Date</th>
            <th rowspan="2" style="width: 12%;">Computers and Peripherals</th>

            @foreach($hardwareItems as $item)
                <th colspan="2">{{ $item['group'] ?? '-' }}</th>
            @endforeach

            <th colspan="2">Software</th>
            <th rowspan="2" style="width: 10%;">Remarks</th>
            <th rowspan="2" style="width: 10%;">Corrective Action</th>
        </tr>

        <tr>
            @foreach($hardwareItems as $item)
                <th>OK</th>
                <th>Not OK</th>
            @endforeach

            <th>✓</th>
            <th>-</th>
        </tr>

        <tr>
            <th colspan="3"></th>

            @foreach($hardwareItems as $item)
                <th colspan="2">{{ $item['label'] ?? '-' }}</th>
            @endforeach

            <th>Setup Anti-Virus</th>
            <th>System Scan and Removal of Malicious Software</th>
            <th></th>
            <th></th>
        </tr>
    </thead>

    <tbody>
        @forelse($records as $index => $record)
            @php
                $device = $record->device;
                $data = $getChecklistData($record);

                $hardware = $data['hardware'] ?? $data['hardwareResponses'] ?? [];
                $software = $data['software'] ?? $data['softwareResponses'] ?? [];

                $recordDate = $record->maintenance_date
                    ? $record->maintenance_date->format('m/d/Y')
                    : '-';

                $deviceLabel = trim(
                    ($device?->type?->name ?? 'Device') . ' ' .
                    ($device?->property_number ? '(' . $device->property_number . ')' : '')
                );
            @endphp

            <tr class="row-height">
                <td>{{ $index + 1 }}</td>
                <td>{{ $recordDate }}</td>
                <td class="left">{{ $deviceLabel }}</td>

                @foreach($hardwareItems as $key => $item)
                    @php $value = $hardware[$key] ?? ''; @endphp

                    <td class="check">{{ $value === 'OK' ? '✓' : '' }}</td>
                    <td class="check">{{ $value === 'Not OK' ? '✓' : '' }}</td>
                @endforeach

                <td class="check">
                    {{ ($software['setup_antivirus'] ?? '') === 'check' ? '✓' : '' }}
                </td>

                <td class="check">
                    {{ ($software['system_scan_removal'] ?? '') === 'check' ? '✓' : (($software['system_scan_removal'] ?? '') === 'dash' ? '-' : '') }}
                </td>

                <td>{{ $record->remarks ?? '' }}</td>
                <td>{{ data_get($data, 'corrective_action', '') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="21">No checked equipment records found.</td>
            </tr>
        @endforelse

        @for($i = $records->count(); $i < 10; $i++)
            <tr class="row-height">
                <td>&nbsp;</td>
                <td></td>
                <td></td>

                @foreach($hardwareItems as $item)
                    <td></td>
                    <td></td>
                @endforeach

                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        @endfor
    </tbody>
</table>

<table class="signature-table">
    <tr>
        <td>
            Checked by:
            <span class="sig-line">
                {{ $firstRecord?->checkedBy?->name ?? auth()->user()->name ?? '' }}
            </span>
            <br>
            Date:
            <span class="sig-date-line">{{ $dateText }}</span>
        </td>

        <td style="text-align: right;">
            Approved by:
            <span class="sig-line"></span>
            <br>
            Date:
            <span class="sig-date-line"></span>
        </td>
    </tr>
</table>

</body>
</html>
