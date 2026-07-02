<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Preventive Maintenance Checklist</title>

    <style>
        /* 8.5 x 13 inches / long coupon bond, landscape */
        @page {
            size: 13in 8.5in;
            margin: 92px 24px 82px 24px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 7.4px;
            color: #111827;
        }

        .page-header {
            position: fixed;
            top: -82px;
            left: 0;
            right: 0;
            height: 82px;
        }

        .page-footer {
            position: fixed;
            left: 0;
            right: 0;
            bottom: -74px;
            height: 72px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .header-table td {
            border: 0;
            padding: 0;
            vertical-align: top;
        }

        .logo-cell {
            width: 54px;
            padding-top: 2px !important;
        }

        .logo {
            width: 44px;
            height: 44px;
        }

        .school-text {
            font-size: 8px;
            line-height: 1.15;
            padding-top: 1px !important;
        }

        .school-name {
            font-size: 9.5px;
            font-weight: bold;
            text-transform: uppercase;
            line-height: 1.05;
        }

        .unit-title {
            position: absolute;
            top: 38px;
            left: 54px;
            font-size: 8.2px;
            white-space: nowrap;
        }

        .header-date {
            position: absolute;
            top: 45px;
            right: 0;
            width: 190px;
            font-size: 8.2px;
            text-align: left;
        }

        .line {
            display: inline-block;
            border-bottom: 1px solid #111827;
            height: 11px;
            vertical-align: bottom;
        }

        .date-line {
            width: 108px;
            text-align: center;
            padding-left: 3px;
            padding-right: 3px;
        }

        .blue-rule-top {
            position: absolute;
            left: 0;
            right: 0;
            top: 56px;
            border-top: 2px solid #1d70b8;
        }

        .office-line-wrap {
            position: absolute;
            top: 61px;
            left: 0;
            width: 380px;
            font-size: 8px;
        }

        .office-line {
            width: 235px;
            text-align: center;
        }

        .form-title {
            position: absolute;
            top: 61px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9.2px;
            font-weight: bold;
            letter-spacing: 0.2px;
            text-transform: uppercase;
        }

        .content {
            width: 100%;
        }

        .main-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            page-break-inside: auto;
        }

        thead {
            display: table-header-group;
        }

        tbody {
            display: table-row-group;
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        .main-table th,
        .main-table td {
            border: 1px solid #111827;
            padding: 2px 2px;
            text-align: center;
            vertical-align: middle;
            word-wrap: break-word;
            overflow-wrap: break-word;
            line-height: 1.05;
        }

        .main-table th {
            font-weight: bold;
        }

        .main-table .left {
            text-align: left;
        }

        .label-row th {
            font-size: 6.5px;
            font-weight: normal;
            line-height: 1.05;
        }

        .status-head {
            font-size: 6.5px;
            line-height: 1;
            font-weight: normal;
        }

        .row-height td {
            height: 25px;
        }

        .check {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 9px;
            font-weight: bold;
            line-height: 1;
        }

        .remarks-cell,
        .action-cell {
            font-size: 6.8px;
            line-height: 1.08;
        }

        .footer-signatures {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-bottom: 8px;
        }

        .footer-signatures td {
            border: 0;
            padding: 0 18px;
            vertical-align: top;
            font-size: 8.5px;
        }

        .sig-label {
            display: inline-block;
            width: 56px;
            font-weight: bold;
        }

        .sig-name-line {
            display: inline-block;
            width: 175px;
            height: 13px;
            border-bottom: 1px solid #111827;
            text-align: center;
            vertical-align: bottom;
        }

        .sig-date-line {
            display: inline-block;
            width: 95px;
            height: 13px;
            border-bottom: 1px solid #111827;
            text-align: center;
            vertical-align: bottom;
        }

        .document-footer {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 16px;
            border-top: 2px solid #1d70b8;
            font-size: 7px;
        }

        .document-footer .code {
            position: absolute;
            left: 0;
            top: 3px;
        }

        .document-footer .rev {
            position: absolute;
            left: 49%;
            top: 3px;
            transform: translateX(-50%);
        }

        .document-footer .effectivity {
            position: absolute;
            right: 0;
            top: 3px;
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

    // Fixed header unit based on the official printed form.
    $fixedUnitName = 'Information and Communications Technology Unit';

    $officeUnitCode = $firstCollege?->code
        ?? $firstOffice?->name
        ?? '';

    $dateSource = $firstRecord?->maintenance_date ?? now();
    $dateText = \Carbon\Carbon::parse($dateSource)->format('m/d/Y');

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

    $displayComputerPeripheral = function ($record) {
        $device = $record->device;

        if (! $device) {
            return '-';
        }

        $computerName = $device->computer_name
            ?: data_get($device->specs, 'computer_name')
            ?: $device->property_number;

        $status = strtoupper($device->condition ?? '');

        return trim($computerName . ($status ? ' (' . $status . ')' : ''));
    };

    $checkedByText = $firstRecord?->checkedBy?->name
        ?? auth()->user()->name
        ?? '';
@endphp

<div class="page-header">
    <table class="header-table">
        <tr>
            <td class="logo-cell">
                @if(file_exists($logoPath))
                    <img src="{{ $logoPath }}" class="logo">
                @endif
            </td>

            <td class="school-text">
                <div>Republic of the Philippines</div>
                <div class="school-name">CATANDUANES STATE UNIVERSITY</div>
                <div>Virac, Catanduanes</div>
            </td>

            <td style="width: 230px;"></td>
        </tr>
    </table>

    <div class="unit-title">{{ $fixedUnitName }}</div>

    <div class="header-date">
        Date:
        <span class="line date-line">{{ $dateText }}</span>
    </div>

    <div class="blue-rule-top"></div>

    <div class="office-line-wrap">
        Office/Unit:
        <span class="line office-line">{{ $officeUnitCode }}</span>
    </div>

    <div class="form-title">PREVENTIVE MAINTENANCE CHECKLIST</div>
</div>

<div class="page-footer">
    <table class="footer-signatures">
        <tr>
            <td style="width: 50%;">
                <span class="sig-label">Checked by:</span>
                <span class="sig-name-line">{{ $checkedByText }}</span>
                <br>
                <span class="sig-label">Date:</span>
                <span class="sig-date-line">{{ $dateText }}</span>
            </td>

            <td style="width: 50%; text-align: right;">
                <span class="sig-label">Approved by:</span>
                <span class="sig-name-line"></span>
                <br>
                <span class="sig-label">Date:</span>
                <span class="sig-date-line"></span>
            </td>
        </tr>
    </table>

    <div class="document-footer">
        <span class="code">CatSU-F-ICTU-05</span>
        <span class="rev">Rev: 0</span>
        <span class="effectivity">Effectivity Date: July 22, 2024</span>
    </div>
</div>

<div class="content">
    <table class="main-table">
        <thead>
            <tr>
                <th rowspan="3" style="width: 5.2%;">Computers and Peripherals</th>

                <th colspan="2" style="width: 7.2%;">System<br>Unit</th>
                <th colspan="2" style="width: 7.2%;">Monitor</th>
                <th colspan="2" style="width: 7.2%;">Keyboard</th>
                <th colspan="2" style="width: 7.2%;">Mouse</th>
                <th colspan="2" style="width: 7.2%;">AVR/UPS</th>
                <th colspan="2" style="width: 7.2%;">Printer</th>
                <th colspan="2" style="width: 7.4%;">Software</th>
                <th rowspan="3" style="width: 10.8%;">Remarks</th>
                <th rowspan="3" style="width: 10.8%;">Corrective<br>Action</th>
            </tr>

            <tr class="label-row">
                @foreach($hardwareItems as $item)
                    <th colspan="2">{{ $item['label'] ?? '-' }}</th>
                @endforeach

                <th>Setup Anti-<br>Virus</th>
                <th>System Scan<br>and Removal<br>of Malicious<br>Software</th>
            </tr>

            <tr class="status-head">
                @foreach($hardwareItems as $item)
                    <th>OK</th>
                    <th>Not<br>OK</th>
                @endforeach

                <th></th>
                <th></th>
            </tr>
        </thead>

        <tbody>
            @forelse($records as $index => $record)
                @php
                    $data = $getChecklistData($record);
                    $hardware = $data['hardware'] ?? $data['hardwareResponses'] ?? [];
                    $software = $data['software'] ?? $data['softwareResponses'] ?? [];
                    $correctiveAction = $record->corrective_action ?? data_get($data, 'corrective_action', '');
                @endphp

                <tr class="row-height">
                    <td class="left">{{ $displayComputerPeripheral($record) }}</td>

                    @foreach($hardwareItems as $key => $item)
                        @php $value = $hardware[$key] ?? ''; @endphp
                        <td class="check">{{ $value === 'OK' ? '✓' : '' }}</td>
                        <td class="check">{{ $value === 'Not OK' ? '✓' : '' }}</td>
                    @endforeach

                    <td class="check">{{ ($software['setup_antivirus'] ?? '') === 'check' ? '✓' : '' }}</td>
                    <td class="check">{{ ($software['system_scan_removal'] ?? '') === 'check' ? '✓' : (($software['system_scan_removal'] ?? '') === 'dash' ? '-' : '') }}</td>

                    <td class="remarks-cell">{{ $record->remarks ?? '' }}</td>
                    <td class="action-cell">{{ $correctiveAction }}</td>
                </tr>
            @empty
                <tr class="row-height">
                    <td colspan="17">No checked equipment records found.</td>
                </tr>
            @endforelse

            @for($i = $records->count(); $i < 10; $i++)
                <tr class="row-height">
                    <td>&nbsp;</td>
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
</div>
</body>
</html>
