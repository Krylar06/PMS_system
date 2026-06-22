@extends('admin.layouts.app')

@section('title', 'Colleges')
@section('page_title', 'Colleges')

@section('content')
@php
    $addBag = $errors->getBag('add');
    $editBag = $errors->getBag('edit');

    $oldNames = old('names', []);
    $oldCodes = old('codes', []);
    $bulkSeedCount = $oldNames ? max(1, min(3, count($oldNames))) : 2;

    $bulkRowsSeed = [];
    for ($i = 0; $i < $bulkSeedCount; $i++) {
        $bulkRowsSeed[] = [
            'name' => $oldNames[$i] ?? '',
            'code' => $oldCodes[$i] ?? '',
            'nameError' => $addBag->first("names.$i"),
            'codeError' => $addBag->first("codes.$i"),
        ];
    }
@endphp
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('collegeManager', () => ({
        addOpen: {{ $addBag->any() ? 'true' : 'false' }},
        editOpen: {{ $editBag->any() ? 'true' : 'false' }},
        deleteOpen: false,
        bulkEnabled: {{ old('names') !== null ? 'true' : 'false' }},

        addSingle: {
            name: @js(old('name', '')),
            code: @js(old('code', '')),
            nameError: @js($addBag->first('name')),
            codeError: @js($addBag->first('code'))
        },

        bulkRows: @json($bulkRowsSeed),

        editCollege: {
            id: @js(old('editing_id') !== null ? (int) old('editing_id') : null),
            name: @js(old('name', '')),
            code: @js(old('code', '')),
            nameError: @js($editBag->first('name')),
            codeError: @js($editBag->first('code'))
        },
        deleteCollegeId: null,

        openAdd() {
            this.addOpen = true;
            this.bulkEnabled = false;
            this.addSingle = { name: '', code: '', nameError: '', codeError: '' };
            this.bulkRows = [
                { name: '', code: '', nameError: '', codeError: '' },
                { name: '', code: '', nameError: '', codeError: '' },
            ];
        },

        addBulkRow() {
            if (this.bulkRows.length < 3) {
                this.bulkRows.push({ name: '', code: '', nameError: '', codeError: '' });
            }
        },

        removeBulkRow() {
            if (this.bulkRows.length > 1) {
                this.bulkRows.pop();
            }
        },

        openEdit(college) {
            this.editCollege = {
                id: college.id,
                name: college.name,
                code: college.code,
                nameError: '',
                codeError: ''
            };
            this.editOpen = true;
        },

        openDelete(id) {
            this.deleteCollegeId = id;
            this.deleteOpen = true;
            this.$nextTick(() => this.$refs.confirmDeleteBtn && this.$refs.confirmDeleteBtn.focus());
        }
    }));
});
</script>
<div
    x-data="collegeManager"
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
            @click="openAdd()"
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
                <div class="flex items-center justify-between gap-3">
                    <label class="text-sm font-medium">Add multiple colleges</label>
                    <button
                        type="button"
                        class="rounded-lg px-3 py-1.5 text-sm font-medium border"
                    x-bind:class="bulkEnabled ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'"
                        @click="bulkEnabled = !bulkEnabled"
                    >
                        <span x-text="bulkEnabled ? 'Bulk: On' : 'Bulk: Off'">OFF</span>
                    </button>


                </div>
            </div>

            <div class="space-y-3">
                <!-- Bulk controls -->
                <div x-show="bulkEnabled" class="flex items-center gap-2">
                    <button
                        type="button"
                        class="rounded-lg bg-gray-100 px-3 py-1.5 text-gray-700 hover:bg-gray-200"
                        @click="removeBulkRow()"
                    >-
                    </button>

                    <input type="hidden" name="count" :value="bulkRows.length">

                    <div class="text-sm text-gray-700">
                        Records: <span class="font-semibold" x-text="bulkRows.length"></span>
                    </div>

                    <button
                        type="button"
                        class="rounded-lg bg-gray-100 px-3 py-1.5 text-gray-700 hover:bg-gray-200"
                        @click="addBulkRow()"
                    >+
                    </button>
                </div>

                <!-- Bulk form -->
                <template x-if="bulkEnabled">
                    <div class="space-y-4">
                        <template x-for="(row, idx) in bulkRows" :key="idx">
                            <div class="space-y-2 rounded-lg border border-gray-200 p-3 bg-gray-50">
                                <div class="text-xs font-semibold text-gray-600" x-text="`College ${idx + 1}`"></div>

                                <div>
                                    <label class="text-sm font-medium">College Name</label>
                                    <input
                                        :name="`names[${idx}]`"
                                        x-model="row.name"
                                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                                        required
                                        :placeholder="`e.g. College ${idx + 1}`"
                                    >
                                    <div class="mt-1 text-sm text-red-600" x-show="row.nameError" x-text="row.nameError"></div>
                                </div>

                                <div>
                                    <label class="text-sm font-medium">Code (optional)</label>
                                    <input
                                        :name="`codes[${idx}]`"
                                        x-model="row.code"
                                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                                        placeholder="Optional (unique)"
                                    >
                                    <div class="mt-1 text-sm text-red-600" x-show="row.codeError" x-text="row.codeError"></div>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>

                <!-- Single form -->
                <template x-if="!bulkEnabled">
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm font-medium">College Name</label>
                            <input
                                name="name"
                                x-model="addSingle.name"
                                class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                                required
                                placeholder="Enter college name"
                            >
                            <div class="mt-1 text-sm text-red-600" x-show="addSingle.nameError" x-text="addSingle.nameError"></div>
                        </div>

                        <div>
                            <label class="text-sm font-medium">Code (optional)</label>
                            <input
                                name="code"
                                x-model="addSingle.code"
                                class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                                placeholder="Optional (unique)"
                            >
                            <div class="mt-1 text-sm text-red-600" x-show="addSingle.codeError" x-text="addSingle.codeError"></div>
                        </div>
                    </div>
                </template>
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
    <x-modal show="editOpen" title="Edit College" >
        <form
            method="POST"
            action="{{ route('admin.colleges.update', '__ID__') }}"
            x-bind:action="'{{ route('admin.colleges.update', '__ID__') }}'.replace('__ID__', editCollege.id)"
            class="space-y-3"
        >
            @csrf
            @method('PUT')

            <input type="hidden" name="editing_id" :value="editCollege.id">

            <div>
                <label class="text-sm font-medium">College Name</label>
                <input
                    name="name"
                    class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                    x-model="editCollege.name"
                    required
                >
                <div class="mt-1 text-sm text-red-600" x-show="editCollege.nameError" x-text="editCollege.nameError"></div>
            </div>

            <div>
                <label class="text-sm font-medium">Code (optional)</label>
                <input
                    name="code"
                    class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                    x-model="editCollege.code"
                >
                <div class="mt-1 text-sm text-red-600" x-show="editCollege.codeError" x-text="editCollege.codeError"></div>
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
                :action="`{{ route('admin.colleges.destroy', ['college' => '__ID__']) }}`.replace('__ID__', deleteCollegeId)"
                @submit="if (!deleteCollegeId) $event.preventDefault()"
                class="flex gap-2"
            >

                @csrf
                @method('DELETE')

                <button type="submit" x-ref="confirmDeleteBtn" class="rounded-lg bg-red-600 px-4 py-2 text-white hover:bg-red-700">Confirm</button>

                <button type="button" class="rounded-lg bg-gray-100 px-4 py-2 text-gray-700 hover:bg-gray-200" @click="deleteOpen = false">
                    Cancel
                </button>
            </form>
        </div>
    </x-modal>
</div>
@endsection