@extends('admin.layouts.app')

@section('title', 'Reports')
@section('page_title', 'Reports')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-semibold text-gray-900">Reports</h1>
        <p class="mt-1 text-sm text-gray-500">Generate inventory, account, checked-equipment, and printable checklist reports.</p>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
        <a href="{{ route('admin.reports.assets') }}" class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-md">
            <div class="text-sm font-semibold uppercase tracking-wide text-blue-600">All Assets</div>
            <h2 class="mt-3 text-lg font-semibold text-gray-900">Assets by Type / Office / College</h2>
            <p class="mt-2 text-sm text-gray-500">Filter all equipment by device type, college, office, or keyword.</p>
        </a>

        @if(auth()->user()?->isAdmin())
            <a href="{{ route('admin.reports.accounts') }}" class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-md">
                <div class="text-sm font-semibold uppercase tracking-wide text-violet-600">Accounts</div>
                <h2 class="mt-3 text-lg font-semibold text-gray-900">Registered Admin / Custodian Accounts</h2>
                <p class="mt-2 text-sm text-gray-500">List user accounts by role with searchable names and emails.</p>
            </a>
        @endif

        <a href="{{ route('admin.reports.checkedEquipment') }}" class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-md">
            <div class="text-sm font-semibold uppercase tracking-wide text-emerald-600">Checked Equipment</div>
            <h2 class="mt-3 text-lg font-semibold text-gray-900">Marked Checked by Admin</h2>
            <p class="mt-2 text-sm text-gray-500">See equipment marked checked, grouped by admin account and date.</p>
        </a>

        <a href="{{ route('admin.reports.checklist') }}" class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-md">
            <div class="text-sm font-semibold uppercase tracking-wide text-orange-600">Checklist</div>
            <h2 class="mt-3 text-lg font-semibold text-gray-900">Printable Add-On Checklist Form</h2>
            <p class="mt-2 text-sm text-gray-500">Generate a printable checklist based on filtered devices.</p>
        </a>
    </div>
</div>
@endsection
