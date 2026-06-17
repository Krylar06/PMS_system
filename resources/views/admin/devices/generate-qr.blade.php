@extends('admin.layouts.app')

@section('title', 'Generate QR')
@section('page_title', 'Generate QR')

@section('content')

<div class="mb-5 flex justify-between">
    <h1 class="text-2xl font-semibold">
        Device QR Codes
    </h1>

    <button
        onclick="window.print()"
        class="rounded-lg bg-blue-600 px-4 py-2 text-white"
    >
        Print
    </button>
</div>

<div class="qr-container">

    @foreach($devices as $device)

        <div class="qr-card">

            <div class="text-lg font-bold">
                {{ $device->property_number }}
            </div>

            <div class="mt-2 flex justify-center">
                {!! $qrCodes[$device->id] !!}
            </div>

            <div class="mt-2 text-sm">
                {{ $device->type?->name }}
            </div>

            <div class="text-xs text-gray-500">
                Serial:
                {{ $device->serial_number ?: 'N/A' }}
            </div>

        </div>

    @endforeach

</div>

@endsection


@push('styles')
<style>

@page {
    size: legal portrait;
    margin: 10mm;
}

.qr-container {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
}

.qr-card {
    width: 3in;
    height: 2in;

    border: 1px dashed black;
    border-radius: 10px;

    padding: 10px;
    text-align: center;

    page-break-inside: avoid;
}

@media print {

    button {
        display: none;
    }

    body {
        background: white;
    }

    .qr-card {
        break-inside: avoid;
    }
}

</style>
@endpush
