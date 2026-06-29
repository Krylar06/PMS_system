@extends('admin.layouts.app')

@section('title', 'Activity Logs')
@section('page_title', 'Activity Logs')

@section('content')
<div class="space-y-5">
    <div>
        <h1 class="text-2xl font-semibold text-gray-900">Activity Logs</h1>
        <p class="mt-1 text-sm text-gray-500">
            A read-only audit trail of who did what, and when.
        </p>
    </div>

    {{-- Filter --}}
    <form method="GET" class="flex flex-wrap items-end gap-3">
        <div>
            <label class="text-sm font-medium">Action</label>
            <select name="action" class="mt-1 rounded-lg border border-gray-300 px-3 py-2 text-sm" onchange="this.form.submit()">
                <option value="">All actions</option>
                @foreach($actions as $action)
                    <option value="{{ $action }}" @selected(request('action') === $action)>
                        {{ ucfirst($action) }}
                    </option>
                @endforeach
            </select>
        </div>

        @if(request('action'))
            <a href="{{ route('admin.logs.index') }}" class="text-sm text-blue-600 hover:underline pb-2">
                Clear filter
            </a>
        @endif
    </form>

    {{-- Mobile cards --}}
    <div class="grid grid-cols-1 gap-3 md:hidden">
        @forelse($logs as $log)
            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-700 capitalize">
                        {{ $log->action }}
                    </span>
                    <span class="text-xs text-gray-400">{{ $log->created_at->format('M d, Y h:i A') }}</span>
                </div>

                <div class="mt-2 text-sm text-gray-900">{{ $log->description }}</div>

                <div class="mt-2 text-xs text-gray-500">
                    By {{ $log->user_name ?? 'Unknown / deleted user' }}
                </div>
            </div>
        @empty
            <div class="rounded-2xl border border-gray-200 bg-white p-6 text-center text-gray-500 shadow-sm">
                No activity recorded yet.
            </div>
        @endforelse
    </div>

    {{-- Desktop table --}}
    <div class="hidden md:block overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-left">
                    <tr>
                        <th class="px-4 py-3 font-semibold text-gray-700">Date/Time</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">User</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Action</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Description</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 whitespace-nowrap text-gray-700">
                                {{ $log->created_at->format('M d, Y h:i A') }}
                            </td>
                            <td class="px-4 py-3 text-gray-700">
                                {{ $log->user_name ?? 'Unknown / deleted user' }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-700 capitalize">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-900">{{ $log->description }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                No activity recorded yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>
        {{ $logs->links() }}
    </div>
</div>
@endsection