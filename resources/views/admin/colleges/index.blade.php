@extends('admin.layouts.app')

@section('title', 'Colleges')
@section('page_title', 'Colleges')

@section('content')
<div
    x-data="{
        addOpen: {{ $errors->any() ? 'true' : 'false' }},
        editOpen: false,
        deleteOpen: false,

        editCollege: { id: null, name: '', code: '' },
        deleteCollegeId: null,

        openEdit(college) {
            this.editCollege = college;
            this.editOpen = true;
        },

        openDelete(id) {
            this.deleteCollegeId = id;
            this.deleteOpen = true;
        }
    }"
    class="space-y-5"
>
    

    {{-- Top section --}}
    <div class="flex items-start justify-between gap-3">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Colleges</h1>
        </div>

        <button
            type="button"
            class="shrink-0 inline-flex items-center rounded-xl bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700"
            @click="addOpen = true"
        >
            + Add College
        </button>
    </div>

    {{-- Mobile cards --}}
    <div class="grid grid-cols-1 gap-3 md:hidden">
        @forelse ($colleges as $c)
            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                <div class="space-y-3">
                    <div>
                        <a
                            class="font-semibold text-blue-700 hover:underline"
                            href="{{ route('admin.offices.index', $c) }}"
                        >
                            {{ $c->name }}
                        </a>
                    </div>

                    <div class="text-sm">
                        <div class="text-gray-500">Code</div>
                        <div class="text-gray-900">{{ $c->code ?: '-' }}</div>
                    </div>

                    <div class="flex flex-wrap gap-2 pt-1">
                        <button
                            type="button"
                            class="rounded-lg bg-gray-900 px-3 py-1.5 text-sm font-medium text-white hover:bg-black"
                            @click="openEdit({
                                id: {{ $c->id }},
                                name: @js($c->name),
                                code: @js($c->code ?? '')
                            })"
                        >
                            Edit
                        </button>

                        <button
                            type="button"
                            class="rounded-lg bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700"
                            @click="openDelete({{ $c->id }})"
                        >
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-2xl border border-gray-200 bg-white p-6 text-center text-gray-500 shadow-sm">
                No colleges found.
            </div>
        @endforelse
    </div>

    {{-- Desktop table --}}
    <div class="hidden md:block overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-left">
                    <tr>
                        <th class="px-4 py-3 font-semibold text-gray-700">Name</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Code</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($colleges as $c)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <a
                                    class="font-medium text-blue-700 hover:underline"
                                    href="{{ route('admin.offices.index', $c) }}"
                                >
                                    {{ $c->name }}
                                </a>
                            </td>

                            <td class="px-4 py-3 text-gray-700">{{ $c->code ?: '-' }}</td>

                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <button
                                        type="button"
                                        class="rounded-lg bg-gray-900 px-3 py-1.5 text-sm font-medium text-white hover:bg-black"
                                        @click="openEdit({
                                            id: {{ $c->id }},
                                            name: @js($c->name),
                                            code: @js($c->code ?? '')
                                        })"
                                    >
                                        Edit
                                    </button>

                                    <button
                                        type="button"
                                        class="rounded-lg bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700"
                                        @click="openDelete({{ $c->id }})"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-gray-500">
                                No colleges found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>
        {{ $colleges->links() }}
    </div>

    {{-- Add modal --}}
    <x-modal show="addOpen" title="Add College">
        <form method="POST" action="{{ route('admin.colleges.store') }}" class="space-y-3">
            @csrf

            <div>
                <label class="text-sm font-medium">College Name</label>
                <input
                    name="name"
                    value="{{ old('name') }}"
                    class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                    required
                >
                @error('name')
                    <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label class="text-sm font-medium">Code (optional)</label>
                <input
                    name="code"
                    value="{{ old('code') }}"
                    class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                >
                @error('code')
                    <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                @enderror
            </div>

            <div class="flex gap-2 pt-2">
                <button class="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">Save</button>
                <button type="button" class="rounded-lg bg-gray-100 px-4 py-2 text-gray-700 hover:bg-gray-200" @click="addOpen = false">
                    Cancel
                </button>
            </div>
        </form>
    </x-modal>

    {{-- Edit modal --}}
    <x-modal show="editOpen" title="Edit College">
        <form
            method="POST"
            action="{{ route('admin.colleges.update', '__ID__') }}"
            x-bind:action="'{{ route('admin.colleges.update', '__ID__') }}'.replace('__ID__', editCollege.id)"
            class="space-y-3"
        >
            @csrf
            @method('PUT')

            <div>
                <label class="text-sm font-medium">College Name</label>
                <input
                    name="name"
                    class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                    x-model="editCollege.name"
                    required
                >
            </div>

            <div>
                <label class="text-sm font-medium">Code (optional)</label>
                <input
                    name="code"
                    class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                    x-model="editCollege.code"
                >
            </div>

            <div class="flex gap-2 pt-2">
                <button class="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">Update</button>
                <button type="button" class="rounded-lg bg-gray-100 px-4 py-2 text-gray-700 hover:bg-gray-200" @click="editOpen = false">
                    Cancel
                </button>
            </div>
        </form>
    </x-modal>

    {{-- Delete modal --}}
    <x-modal show="deleteOpen" title="Delete College">
        <div class="space-y-3">
            <div class="text-sm text-gray-700">
                Are you sure you want to delete this college?
            </div>

            <form
                method="POST"
                :action="`{{ url('/admin/colleges') }}/${deleteCollegeId}`"
                class="flex gap-2"
            >
                @csrf
                @method('DELETE')

                <button class="rounded-lg bg-red-600 px-4 py-2 text-white hover:bg-red-700">Yes, Delete</button>
                <button type="button" class="rounded-lg bg-gray-100 px-4 py-2 text-gray-700 hover:bg-gray-200" @click="deleteOpen = false">
                    Cancel
                </button>
            </form>
        </div>
    </x-modal>
</div>
@endsection