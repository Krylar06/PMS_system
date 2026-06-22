@extends('admin.layouts.app')

@section('title', 'Staff')
@section('page_title', 'Staff')

@section('content')
@php
    $addBag = $errors->getBag('add');
    $editBag = $errors->getBag('edit');

    $oldStaffRows = old('staff', []);
    $bulkSeedCount = $oldStaffRows ? max(1, min(3, count($oldStaffRows))) : 2;

    $bulkRowsSeed = [];
    for ($i = 0; $i < $bulkSeedCount; $i++) {
        $bulkRowsSeed[] = [
            'first_name' => $oldStaffRows[$i]['first_name'] ?? '',
            'last_name' => $oldStaffRows[$i]['last_name'] ?? '',
            'position' => $oldStaffRows[$i]['position'] ?? '',
            'email' => $oldStaffRows[$i]['email'] ?? '',
            'phone' => $oldStaffRows[$i]['phone'] ?? '',
            'is_active' => $oldStaffRows ? isset($oldStaffRows[$i]['is_active']) : true,
            'firstNameError' => $addBag->first("staff.$i.first_name"),
            'lastNameError' => $addBag->first("staff.$i.last_name"),
            'emailError' => $addBag->first("staff.$i.email"),
        ];
    }
@endphp
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('staffManager', () => ({
        addOpen: {{ $addBag->any() ? 'true' : 'false' }},
        editOpen: {{ $editBag->any() ? 'true' : 'false' }},
        deleteOpen: false,
        bulkEnabled: {{ old('staff') !== null ? 'true' : 'false' }},

        addSingle: {
            first_name: @js(old('first_name', '')),
            last_name: @js(old('last_name', '')),
            position: @js(old('position', '')),
            email: @js(old('email', '')),
            phone: @js(old('phone', '')),
            is_active: {{ old('first_name') !== null ? (old('is_active') ? 'true' : 'false') : 'true' }},
            firstNameError: @js($addBag->first('first_name')),
            lastNameError: @js($addBag->first('last_name')),
            emailError: @js($addBag->first('email'))
        },

        bulkRows: @json($bulkRowsSeed),

        editStaff: {
            id: @js(old('editing_id') !== null ? (int) old('editing_id') : null),
            first_name: @js(old('first_name', '')),
            last_name: @js(old('last_name', '')),
            position: @js(old('position', '')),
            email: @js(old('email', '')),
            phone: @js(old('phone', '')),
            is_active: {{ old('editing_id') !== null ? (old('is_active') ? 'true' : 'false') : 'true' }},
            firstNameError: @js($editBag->first('first_name')),
            lastNameError: @js($editBag->first('last_name')),
            emailError: @js($editBag->first('email'))
        },

        deleteStaffId: null,

        openAdd() {
            this.addOpen = true;
            this.bulkEnabled = false;
            this.addSingle = {
                first_name: '', last_name: '', position: '', email: '', phone: '',
                is_active: true,
                firstNameError: '', lastNameError: '', emailError: ''
            };
            this.bulkRows = [
                { first_name: '', last_name: '', position: '', email: '', phone: '', is_active: true, firstNameError: '', lastNameError: '', emailError: '' },
                { first_name: '', last_name: '', position: '', email: '', phone: '', is_active: true, firstNameError: '', lastNameError: '', emailError: '' },
            ];
        },

        addBulkRow() {
            if (this.bulkRows.length < 3) {
                this.bulkRows.push({ first_name: '', last_name: '', position: '', email: '', phone: '', is_active: true, firstNameError: '', lastNameError: '', emailError: '' });
            }
        },

        removeBulkRow() {
            if (this.bulkRows.length > 1) {
                this.bulkRows.pop();
            }
        },

        openEdit(staff) {
            this.editStaff = {
                id: staff.id,
                first_name: staff.first_name ?? '',
                last_name: staff.last_name ?? '',
                position: staff.position ?? '',
                email: staff.email ?? '',
                phone: staff.phone ?? '',
                is_active: !!staff.is_active,
                firstNameError: '',
                lastNameError: '',
                emailError: ''
            };
            this.editOpen = true;
        },

        openDelete(id) {
            this.deleteStaffId = id;
            this.deleteOpen = true;
            this.$nextTick(() => this.$refs.confirmDeleteBtn && this.$refs.confirmDeleteBtn.focus());
        }
    }));
});
</script>
<div
    x-data="staffManager"
    class="space-y-5"
>
    {{-- Breadcrumb --}}
    <div class="text-sm text-gray-500 leading-6 break-words">
        <a class="text-blue-600 hover:underline" href="{{ route('admin.colleges.index') }}">Colleges</a>
        <span class="mx-1">/</span>
        <a class="text-blue-600 hover:underline" href="{{ route('admin.offices.index', $office->college) }}">
            {{ $office->college->name }}
        </a>
        <span class="mx-1">/</span>
        <span class="text-gray-700 font-medium">{{ $office->name }}</span>
        <span class="mx-1">/</span>
        <span>Staff</span>
    </div>

    {{-- Top section --}}
    <div class="flex items-start justify-between gap-3">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Staff in {{ $office->name }}</h1>
        </div>

        <div class="flex flex-wrap gap-2">
            <a
                href="{{ route('admin.offices.preventiveMaintenance.export', $office) }}"
                class="shrink-0 inline-flex items-center rounded-xl bg-green-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-green-700"
            >
                Export Excel Report
            </a>

            <button
                type="button"
                class="shrink-0 inline-flex items-center rounded-xl bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700"
                @click="openAdd()"
            >
                + Add Staff
            </button>
        </div>
    </div>

    @if(session('error'))
        <div class="rounded-xl bg-red-100 px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    {{-- Mobile cards --}}
    <div class="grid grid-cols-1 gap-3 md:hidden">
        @forelse($staff as $s)
            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <a
                            class="font-semibold text-blue-700 hover:underline"
                            href="{{ route('admin.staff.devices.index', $s) }}"
                        >
                            {{ $s->last_name }}, {{ $s->first_name }}
                        </a>

                        <div class="mt-1 text-sm text-gray-500">
                            {{ $s->position ?: 'No position set' }}
                        </div>
                    </div>

                    <div>
                        @if($s->is_active)
                            <span class="inline-flex rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-700">
                                Active
                            </span>
                        @else
                            <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-700">
                                Inactive
                            </span>
                        @endif
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-1 gap-3 text-sm">
                    <div>
                        <div class="text-gray-500">Email</div>
                        <div class="break-all text-gray-900">{{ $s->email ?: '-' }}</div>
                    </div>

                    <div>
                        <div class="text-gray-500">Phone</div>
                        <div class="text-gray-900">{{ $s->phone ?: '-' }}</div>
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap gap-2">
                    <a
                        href="{{ route('admin.staff.devices.index', $s) }}"
                        class="rounded-lg bg-green-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-green-700"
                    >
                        Devices
                    </a>

                    <button
                        type="button"
                        class="rounded-lg bg-gray-900 px-3 py-1.5 text-sm font-medium text-white hover:bg-black"
                        @click="openEdit({
                            id: {{ $s->id }},
                            first_name: @js($s->first_name),
                            last_name: @js($s->last_name),
                            position: @js($s->position ?? ''),
                            email: @js($s->email ?? ''),
                            phone: @js($s->phone ?? ''),
                            is_active: {{ $s->is_active ? 'true' : 'false' }}
                        })"
                    >
                        Edit
                    </button>

                    <button
                        type="button"
                        class="rounded-lg bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700"
                        @click="openDelete({{ $s->id }})"
                    >
                        Delete
                    </button>
                </div>
            </div>
        @empty
            <div class="rounded-2xl border border-gray-200 bg-white p-6 text-center text-gray-500 shadow-sm">
                No staff found.
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
                        <th class="px-4 py-3 font-semibold text-gray-700">Position</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Email</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Phone</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Status</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">
                    @forelse($staff as $s)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <a
                                    class="font-medium text-blue-700 hover:underline"
                                    href="{{ route('admin.staff.devices.index', $s) }}"
                                >
                                    {{ $s->last_name }}, {{ $s->first_name }}
                                </a>
                            </td>

                            <td class="px-4 py-3 text-gray-700">{{ $s->position ?: '-' }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $s->email ?: '-' }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $s->phone ?: '-' }}</td>

                            <td class="px-4 py-3">
                                @if($s->is_active)
                                    <span class="inline-flex rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-700">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-700">
                                        Inactive
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <a
                                        href="{{ route('admin.staff.devices.index', $s) }}"
                                        class="rounded-lg bg-green-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-green-700"
                                    >
                                        Devices
                                    </a>

                                    <button
                                        type="button"
                                        class="rounded-lg bg-gray-900 px-3 py-1.5 text-sm font-medium text-white hover:bg-black"
                                        @click="openEdit({
                                            id: {{ $s->id }},
                                            first_name: @js($s->first_name),
                                            last_name: @js($s->last_name),
                                            position: @js($s->position ?? ''),
                                            email: @js($s->email ?? ''),
                                            phone: @js($s->phone ?? ''),
                                            is_active: {{ $s->is_active ? 'true' : 'false' }}
                                        })"
                                    >
                                        Edit
                                    </button>

                                    <button
                                        type="button"
                                        class="rounded-lg bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700"
                                        @click="openDelete({{ $s->id }})"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                No staff found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>
        {{ $staff->links() }}
    </div>

    {{-- Add modal --}}
    <x-modal show="addOpen" title="Add Staff">
        <form method="POST" action="{{ route('admin.staff.store', $office) }}" class="space-y-3">
            @csrf

            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-gray-700">Add multiple staff</span>
                <button
                    type="button"
                    class="rounded-lg px-3 py-1.5 text-sm font-medium border"
                    :class="bulkEnabled ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-300'"
                    @click="bulkEnabled = !bulkEnabled"
                >
                    <span x-text="bulkEnabled ? 'Bulk: On' : 'Bulk: Off'"></span>
                </button>
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
                                <div class="text-xs font-semibold text-gray-600" x-text="`Staff ${idx + 1}`"></div>

                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="text-sm font-medium">First Name</label>
                                        <input
                                            :name="`staff[${idx}][first_name]`"
                                            x-model="row.first_name"
                                            class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                                            required
                                        >
                                        <div class="mt-1 text-sm text-red-600" x-show="row.firstNameError" x-text="row.firstNameError"></div>
                                    </div>

                                    <div>
                                        <label class="text-sm font-medium">Last Name</label>
                                        <input
                                            :name="`staff[${idx}][last_name]`"
                                            x-model="row.last_name"
                                            class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                                            required
                                        >
                                        <div class="mt-1 text-sm text-red-600" x-show="row.lastNameError" x-text="row.lastNameError"></div>
                                    </div>
                                </div>

                                <div>
                                    <label class="text-sm font-medium">Position</label>
                                    <input
                                        :name="`staff[${idx}][position]`"
                                        x-model="row.position"
                                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                                    >
                                </div>

                                <div>
                                    <label class="text-sm font-medium">Email</label>
                                    <input
                                        :name="`staff[${idx}][email]`"
                                        x-model="row.email"
                                        type="email"
                                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                                    >
                                    <div class="mt-1 text-sm text-red-600" x-show="row.emailError" x-text="row.emailError"></div>
                                </div>

                                <div>
                                    <label class="text-sm font-medium">Phone</label>
                                    <input
                                        :name="`staff[${idx}][phone]`"
                                        x-model="row.phone"
                                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                                    >
                                </div>

                                <label class="flex items-center gap-2 text-sm">
                                    <input
                                        type="checkbox"
                                        :name="`staff[${idx}][is_active]`"
                                        value="1"
                                        x-model="row.is_active"
                                    >
                                    Active
                                </label>
                            </div>
                        </template>
                    </div>
                </template>

                <!-- Single form -->
                <template x-if="!bulkEnabled">
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm font-medium">First Name</label>
                            <input name="first_name" x-model="addSingle.first_name" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2" required>
                            <div class="mt-1 text-sm text-red-600" x-show="addSingle.firstNameError" x-text="addSingle.firstNameError"></div>
                        </div>

                        <div>
                            <label class="text-sm font-medium">Last Name</label>
                            <input name="last_name" x-model="addSingle.last_name" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2" required>
                            <div class="mt-1 text-sm text-red-600" x-show="addSingle.lastNameError" x-text="addSingle.lastNameError"></div>
                        </div>

                        <div>
                            <label class="text-sm font-medium">Position</label>
                            <input name="position" x-model="addSingle.position" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2">
                        </div>

                        <div>
                            <label class="text-sm font-medium">Email</label>
                            <input name="email" type="email" x-model="addSingle.email" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2">
                            <div class="mt-1 text-sm text-red-600" x-show="addSingle.emailError" x-text="addSingle.emailError"></div>
                        </div>

                        <div>
                            <label class="text-sm font-medium">Phone</label>
                            <input name="phone" x-model="addSingle.phone" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2">
                        </div>

                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" name="is_active" value="1" x-model="addSingle.is_active">
                            Active
                        </label>
                    </div>
                </template>
            </div>

            <div class="flex gap-2 pt-2">
                <button class="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">Save</button>
                <button type="button" class="rounded-lg bg-gray-100 px-4 py-2 text-gray-700 hover:bg-gray-200" @click="addOpen=false">Cancel</button>
            </div>
        </form>
    </x-modal>

    {{-- Edit modal --}}
    <x-modal show="editOpen" title="Edit Staff">
        <form
            method="POST"
            :action="`{{ url('/offices/'.$office->id.'/staff') }}/${editStaff.id}`"
            class="space-y-3"
        >
            @csrf
            @method('PUT')

            <input type="hidden" name="editing_id" :value="editStaff.id">

            <div>
                <label class="text-sm font-medium">First Name</label>
                <input name="first_name" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2" x-model="editStaff.first_name" required>
                <div class="mt-1 text-sm text-red-600" x-show="editStaff.firstNameError" x-text="editStaff.firstNameError"></div>
            </div>

            <div>
                <label class="text-sm font-medium">Last Name</label>
                <input name="last_name" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2" x-model="editStaff.last_name" required>
                <div class="mt-1 text-sm text-red-600" x-show="editStaff.lastNameError" x-text="editStaff.lastNameError"></div>
            </div>

            <div>
                <label class="text-sm font-medium">Position</label>
                <input name="position" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2" x-model="editStaff.position">
            </div>

            <div>
                <label class="text-sm font-medium">Email</label>
                <input name="email" type="email" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2" x-model="editStaff.email">
                <div class="mt-1 text-sm text-red-600" x-show="editStaff.emailError" x-text="editStaff.emailError"></div>
            </div>

            <div>
                <label class="text-sm font-medium">Phone</label>
                <input name="phone" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2" x-model="editStaff.phone">
            </div>

            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="is_active" value="1" x-model="editStaff.is_active">
                Active
            </label>

            <div class="flex gap-2 pt-2">
                <button class="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">Update</button>
                <button type="button" class="rounded-lg bg-gray-100 px-4 py-2 text-gray-700 hover:bg-gray-200" @click="editOpen=false">Cancel</button>
            </div>
        </form>
    </x-modal>

    {{-- Delete modal --}}
    <x-modal show="deleteOpen" title="Delete Staff">
        <div class="space-y-3">
            <div class="text-sm text-gray-700">
                Are you sure you want to delete this staff member?
            </div>

            <form
                method="POST"
                :action="`{{ url('/offices/'.$office->id.'/staff') }}/${deleteStaffId}`"
                @submit="if (!deleteStaffId) $event.preventDefault()"
                class="flex gap-2"
            >
                @csrf
                @method('DELETE')

                <button type="submit" x-ref="confirmDeleteBtn" class="rounded-lg bg-red-600 px-4 py-2 text-white hover:bg-red-700">Confirm</button>
                <button type="button" class="rounded-lg bg-gray-100 px-4 py-2 text-gray-700 hover:bg-gray-200" @click="deleteOpen=false">Cancel</button>
            </form>
        </div>
    </x-modal>
</div>
@endsection