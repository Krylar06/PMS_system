@php
    $assignment = $device->currentAssignment;
    $staff = $assignment?->staff;
    $office = $staff?->office;
    $college = $office?->college;

    $markStatus = function ($value, $target) {
        return $value === $target ? '✓' : '';
    };

    $softwareMark = function ($value) {
        return $value === 'check' ? '✓' : '-';
    };
@endphp
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Preventive Maintenance Checklist</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 10px;
            color: #111827;
            margin: 0;
        }
        .page {
            width: 100%;
            padding: 18px 20px;
        }
        .title {
            text-align: center;
            font-weight: bold;
            font-size: 16px;
            letter-spacing: .5px;
            text-transform: uppercase;
            margin-bottom: 16px;
        }
        .meta {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .meta td {
            padding: 4px 6px;
            vertical-align: top;
            font-size: 10px;
        }
        .label {
            font-weight: bold;
            width: 85px;
            white-space: nowrap;
        }
        .line {
            border-bottom: 1px solid #111827;
            min-height: 14px;
        }
        table.checklist {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        table.checklist th,
        table.checklist td {
            border: 1px solid #111827;
            padding: 5px 4px;
            text-align: center;
            vertical-align: middle;
            word-wrap: break-word;
        }
        table.checklist th {
            font-weight: bold;
            background: #f3f4f6;
        }
        .left { text-align: left !important; }
        .asset-col { width: 21%; }
        .status-col { width: 5%; }
        .software-col { width: 7%; }
        .remarks-col { width: 12%; }
        .action-col { width: 12%; }
        .small { font-size: 8px; line-height: 1.15; }
        .signatures {
            width: 100%;
            margin-top: 22px;
            border-collapse: collapse;
        }
        .signatures td {
            width: 50%;
            padding: 6px 20px;
            vertical-align: bottom;
            font-size: 10px;
        }
        .sig-line {
            display: inline-block;
            min-width: 190px;
            border-bottom: 1px solid #111827;
            padding: 0 8px 2px 8px;
            text-align: center;
            min-height: 16px;
        }
        .date-line {
            display: inline-block;
            min-width: 105px;
            border-bottom: 1px solid #111827;
            padding: 0 8px 2px 8px;
            text-align: center;
            min-height: 16px;
        }
        .blank-space {
            height: 24px;
        }
    </style>
</head>
<body>
<div class="page">
    <div class="title">Preventive Maintenance Checklist</div>

    <table class="meta">
        <tr>
            <td class="label">Office/Unit:</td>
            <td class="line">{{ $office?->name ?? 'Unassigned' }}</td>
            <td class="label">College:</td>
            <td class="line">{{ $college?->name ?? '-' }}</td>
            <td class="label">Date:</td>
            <td class="line">{{ \Carbon\Carbon::parse($maintenanceDate)->format('m/d/Y') }}</td>
        </tr>
    </table>

    <table class="checklist">
        <thead>
            <tr>
                <th rowspan="2" class="asset-col">Computers and Peripherals</th>
                <th colspan="2">System Unit<br><span class="small">Check for power on</span></th>
                <th colspan="2">Monitor<br><span class="small">Check display</span></th>
                <th colspan="2">Keyboard<br><span class="small">Check keys</span></th>
                <th colspan="2">Mouse<br><span class="small">Check mouse left/right buttons</span></th>
                <th colspan="2">AVR/UPS<br><span class="small">Check for power recovery</span></th>
                <th colspan="2">Printer<br><span class="small">Check printout</span></th>
                <th colspan="2">Software</th>
                <th rowspan="2" class="remarks-col">Remarks</th>
                <th rowspan="2" class="action-col">Corrective Action</th>
            </tr>
            <tr>
                <th class="status-col">OK</th><th class="status-col">Not OK</th>
                <th class="status-col">OK</th><th class="status-col">Not OK</th>
                <th class="status-col">OK</th><th class="status-col">Not OK</th>
                <th class="status-col">OK</th><th class="status-col">Not OK</th>
                <th class="status-col">OK</th><th class="status-col">Not OK</th>
                <th class="status-col">OK</th><th class="status-col">Not OK</th>
                <th class="software-col small">Setup Anti-Virus</th>
                <th class="software-col small">System Scan and Removal of Malicious Software</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="left">
                    <strong>{{ $device->type?->name ?? 'Device' }}</strong><br>
                    Property #: {{ $device->property_number }}<br>
                    Serial #: {{ $device->serial_number ?: '-' }}<br>
                    @if($device->brand || $device->model)
                        {{ trim(($device->brand ?? '') . ' ' . ($device->model ?? '')) }}
                    @endif
                </td>

                <td>{{ $markStatus($checklistValues['system_unit'] ?? '', 'OK') }}</td>
                <td>{{ $markStatus($checklistValues['system_unit'] ?? '', 'Not OK') }}</td>

                <td>{{ $markStatus($checklistValues['monitor'] ?? '', 'OK') }}</td>
                <td>{{ $markStatus($checklistValues['monitor'] ?? '', 'Not OK') }}</td>

                <td>{{ $markStatus($checklistValues['keyboard'] ?? '', 'OK') }}</td>
                <td>{{ $markStatus($checklistValues['keyboard'] ?? '', 'Not OK') }}</td>

                <td>{{ $markStatus($checklistValues['mouse'] ?? '', 'OK') }}</td>
                <td>{{ $markStatus($checklistValues['mouse'] ?? '', 'Not OK') }}</td>

                <td>{{ $markStatus($checklistValues['avr_ups'] ?? '', 'OK') }}</td>
                <td>{{ $markStatus($checklistValues['avr_ups'] ?? '', 'Not OK') }}</td>

                <td>{{ $markStatus($checklistValues['printer'] ?? '', 'OK') }}</td>
                <td>{{ $markStatus($checklistValues['printer'] ?? '', 'Not OK') }}</td>

                <td>{{ $softwareMark($softwareValues['software_anti_virus'] ?? '') }}</td>
                <td>{{ $softwareMark($softwareValues['software_scan_remove'] ?? '') }}</td>

                <td class="left">{{ $remarks }}</td>
                <td class="left">{{ $correctiveAction }}</td>
            </tr>

            @for($i = 0; $i < 7; $i++)
                <tr>
                    <td class="blank-space">&nbsp;</td>
                    @for($j = 0; $j < 16; $j++)
                        <td>&nbsp;</td>
                    @endfor
                </tr>
            @endfor
        </tbody>
    </table>

    <table class="signatures">
        <tr>
            <td>
                Checked by:
                <span class="sig-line">{{ $checkedBy?->name ?? '' }}</span>
                Date:
                <span class="date-line">{{ \Carbon\Carbon::parse($maintenanceDate)->format('m/d/Y') }}</span>
            </td>
            <td>
                Approved by:
                <span class="sig-line"></span>
                Date:
                <span class="date-line"></span>
            </td>
        </tr>
    </table>
</div>
</body>
</html>
