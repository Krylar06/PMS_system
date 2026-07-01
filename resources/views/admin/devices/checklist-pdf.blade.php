<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Maintenance Checklist</title>

    <style>
        @page {
            size: legal landscape;
            margin: 10mm;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 10px;
            color: #111827;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #111827;
            padding: 5px;
            vertical-align: middle;
        }

        th {
            font-weight: bold;
            text-align: center;
            background: #f3f4f6;
        }

        .title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .info-table td {
            height: 24px;
        }

        .center {
            text-align: center;
        }

        .left {
            text-align: left;
        }

        .box {
            display: inline-block;
            width: 15px;
            height: 15px;
            border: 1.5px solid #111827;
            text-align: center;
            line-height: 14px;
            font-size: 12px;
            font-weight: bold;
        }

        .blank-area {
            height: 95px;
            border: 1px solid #111827;
        }

        .signature-table td {
            border: none;
            padding-top: 18px;
            font-size: 10px;
        }

        .line {
            border-bottom: 1px solid #111827;
            height: 18px;
        }
    </style>
</head>

<body>
@php
    $assignment = $device->currentAssignment;
    $staff = $assignment?->staff;
    $office = $staff?->office;
    $college = $office?->college;

    $hardware = $hardwareResponses ?? ($checklistValues ?? []);
    $software = $softwareResponses ?? ($softwareValues ?? []);

    $checkedByName = $checkedBy?->name ?? $record?->checkedBy?->name ?? '';
    $dateText = \Carbon\Carbon::parse($dateChecked)->format('m/d/Y');
@endphp

<div class="title">PREVENTIVE MAINTENANCE CHECKLIST</div>

<table class="info-table">
    <tr>
        <td style="width: 20%;"><strong>Date:</strong> {{ $dateText }}</td>
        <td style="width: 30%;"><strong>Office/Unit:</strong> {{ $office?->name ?? 'Unassigned' }}</td>
        <td style="width: 30%;"><strong>College:</strong> {{ $college?->name ?? '-' }}</td>
        <td style="width: 20%;"><strong>Checked By:</strong> {{ $checkedByName }}</td>
    </tr>
    <tr>
        <td><strong>Device Type:</strong> {{ $device->type?->name ?? '-' }}</td>
        <td><strong>Property No.:</strong> {{ $device->property_number }}</td>
        <td><strong>Serial No.:</strong> {{ $device->serial_number ?: '-' }}</td>
        <td><strong>Computer Name:</strong> {{ $device->computer_name ?: '-' }}</td>
    </tr>
</table>

<br>

<table>
    <thead>
        <tr>
            <th style="width: 18%;">Section</th>
            <th style="width: 46%;">Checklist Item</th>
            <th style="width: 12%;">OK</th>
            <th style="width: 12%;">Not OK</th>
            <th style="width: 12%;">Remarks</th>
        </tr>
    </thead>

    <tbody>
        @foreach($checklistItems as $key => $item)
            @php
                $value = $hardware[$key] ?? '';
            @endphp

            <tr>
                <td class="left">{{ $item['group'] ?? '-' }}</td>
                <td class="left">{{ $item['label'] ?? '-' }}</td>

                <td class="center">
                    <span class="box">{{ $value === 'OK' ? '✓' : '' }}</span>
                </td>

                <td class="center">
                    <span class="box">{{ $value === 'Not OK' ? '✓' : '' }}</span>
                </td>

                <td></td>
            </tr>
        @endforeach

        @foreach($softwareItems as $key => $label)
            @php
                $value = $software[$key] ?? '';
            @endphp

            <tr>
                <td class="left">Software</td>
                <td class="left">{{ $label }}</td>

                <td class="center">
                    <span class="box">{{ $value === 'check' ? '✓' : '' }}</span>
                </td>

                <td class="center">
                    <span class="box">{{ $value === 'dash' ? '-' : '' }}</span>
                </td>

                <td></td>
            </tr>
        @endforeach
    </tbody>
</table>

<br>

<table>
    <tr>
        <th style="width: 50%;">Remarks</th>
        <th style="width: 50%;">Corrective Action</th>
    </tr>
    <tr>
        <td class="blank-area">{{ $remarks ?? '' }}</td>
        <td class="blank-area">{{ $correctiveAction ?? '' }}</td>
    </tr>
</table>

<br>

<table class="signature-table">
    <tr>
        <td style="width: 50%;">
            Checked by:
            <div class="line">{{ $checkedByName }}</div>
            Date:
            <div class="line">{{ $dateText }}</div>
        </td>

        <td style="width: 50%;">
            Approved by:
            <div class="line"></div>
            Date:
            <div class="line"></div>
        </td>
    </tr>
</table>

</body>
</html>
