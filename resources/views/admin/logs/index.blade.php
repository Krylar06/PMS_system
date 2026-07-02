@extends('admin.layouts.app')

@section('title', 'Activity Logs')
@section('page_title', 'Activity Logs')

@section('content')
<div class="space-y-5">
    <div>
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Activity Logs</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            A read-only audit trail of who did what, and when.
        </p>
    </div>

    {{-- Filter --}}
    <form method="GET" class="flex flex-wrap items-end gap-3">
        <div>
            <label class="text-sm font-medium dark:text-gray-300">Action</label>
            <select name="action" class="mt-1 rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white" onchange="this.form.submit()">
                <option value="">All actions</option>
                @foreach($actions as $action)
                    <option value="{{ $action }}" @selected(request('action') === $action)>
                        {{ ucfirst($action) }}
                    </option>
                @endforeach
            </select>
        </div>

        @if(request('action'))
            <a href="{{ route('admin.logs.index') }}" class="text-sm text-blue-600 hover:underline pb-2 dark:text-blue-400">
                Clear filter
            </a>
        @endif
    </form>

    {{-- Mobile cards --}}
    <div class="grid grid-cols-1 gap-3 md:hidden">
        @forelse($logs as $log)
            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="flex items-start justify-between gap-3">
                    <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-700 capitalize dark:bg-gray-700 dark:text-gray-300">
                        {{ $log->action }}
                    </span>
                    <span class="text-xs text-gray-400 dark:text-gray-500">{{ $log->created_at->format('M d, Y h:i A') }}</span>
                </div>

                <div class="mt-2 text-sm text-gray-900 dark:text-white">{{ $log->description }}</div>

                <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                    By {{ $log->user_name ?? 'Unknown / deleted user' }}
                </div>
            </div>
        @empty
            <div class="rounded-2xl border border-gray-200 bg-white p-6 text-center text-gray-500 shadow-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                No activity recorded yet.
            </div>
        @endforelse
    </div>

    {{-- Desktop table --}}
    <div class="hidden md:block overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-left dark:bg-gray-900/40">
                    <tr>
                        <th class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">Date/Time</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">User</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">Action</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">Description</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                            <td class="px-4 py-3 whitespace-nowrap text-gray-700 dark:text-gray-300">
                                {{ $log->created_at->format('M d, Y h:i A') }}
                            </td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                                {{ $log->user_name ?? 'Unknown / deleted user' }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-700 capitalize dark:bg-gray-700 dark:text-gray-300">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-900 dark:text-white">{{ $log->description }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
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