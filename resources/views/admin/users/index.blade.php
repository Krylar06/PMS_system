@extends('admin.layouts.app')

@section('title', 'Users')
@section('page_title', 'User Accounts')

@section('content')
@php
    $addBag = $errors->getBag('add');
    $editBag = $errors->getBag('edit');
    $roles = \App\Models\User::ROLES;
@endphp
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('userManager', () => ({
        addOpen: {{ $addBag->any() ? 'true' : 'false' }},
        editOpen: {{ $editBag->any() ? 'true' : 'false' }},
        deleteOpen: false,

        addSingle: {
            name: @js(old('name', '')),
            email: @js(old('email', '')),
            role: @js(old('role', 'custodian')),
            password: '',
            password_confirmation: '',
            nameError: @js($addBag->first('name')),
            emailError: @js($addBag->first('email')),
            roleError: @js($addBag->first('role')),
            passwordError: @js($addBag->first('password'))
        },

        editUser: {
            id: @js(old('editing_id') !== null ? (int) old('editing_id') : null),
            name: @js(old('name', '')),
            email: @js(old('email', '')),
            role: @js(old('role', '')),
            password: '',
            password_confirmation: '',
            nameError: @js($editBag->first('name')),
            emailError: @js($editBag->first('email')),
            roleError: @js($editBag->first('role')),
            passwordError: @js($editBag->first('password'))
        },

        deleteUserId: null,

        openAdd() {
            this.addOpen = true;
            this.addSingle = {
                name: '', email: '', role: 'custodian', password: '', password_confirmation: '',
                nameError: '', emailError: '', roleError: '', passwordError: ''
            };
        },

        openEdit(user) {
            this.editUser = {
                id: user.id,
                name: user.name,
                email: user.email,
                role: user.role,
                password: '',
                password_confirmation: '',
                nameError: '', emailError: '', roleError: '', passwordError: ''
            };
            this.editOpen = true;
        },

        openDelete(id) {
            this.deleteUserId = id;
            this.deleteOpen = true;
            this.$nextTick(() => this.$refs.confirmDeleteBtn && this.$refs.confirmDeleteBtn.focus());
        }
    }));
});
</script>
<div x-data="userManager" class="space-y-5">
    <div class="flex items-start justify-between gap-3">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">User Accounts</h1>
            <p class="mt-1 text-sm text-gray-500">
                Manage who can sign in and what they're allowed to do.
            </p>
        </div>

        <button
            type="button"
            class="shrink-0 inline-flex items-center rounded-xl bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700"
            @click="openAdd()"
        >
            + Add User
        </button>
    </div>

    {{-- Mobile cards --}}
    <div class="grid grid-cols-1 gap-3 md:hidden">
        @forelse($users as $u)
            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="font-semibold text-gray-900">{{ $u->name }}</div>
                        <div class="mt-1 text-sm text-gray-500 break-all">{{ $u->email }}</div>
                    </div>

                    <span class="inline-flex shrink-0 rounded-full {{ $u->isAdmin() ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700' }} px-2.5 py-1 text-xs font-medium">
                        {{ $u->roleLabel() }}
                    </span>
                </div>

                <div class="mt-4 flex flex-wrap gap-2">
                    <button
                        type="button"
                        class="rounded-lg bg-gray-900 px-3 py-1.5 text-sm font-medium text-white hover:bg-black"
                        @click="openEdit({
                            id: {{ $u->id }},
                            name: @js($u->name),
                            email: @js($u->email),
                            role: @js($u->role)
                        })"
                    >
                        Edit
                    </button>

                    @if($u->id !== auth()->id())
                        <button
                            type="button"
                            class="rounded-lg bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700"
                            @click="openDelete({{ $u->id }})"
                        >
                            Delete
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="rounded-2xl border border-gray-200 bg-white p-6 text-center text-gray-500 shadow-sm">
                No users found.
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
                        <th class="px-4 py-3 font-semibold text-gray-700">Email</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Role</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($users as $u)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $u->name }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $u->email }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full {{ $u->isAdmin() ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700' }} px-2.5 py-1 text-xs font-medium">
                                    {{ $u->roleLabel() }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <button
                                        type="button"
                                        class="rounded-lg bg-gray-900 px-3 py-1.5 text-sm font-medium text-white hover:bg-black"
                                        @click="openEdit({
                                            id: {{ $u->id }},
                                            name: @js($u->name),
                                            email: @js($u->email),
                                            role: @js($u->role)
                                        })"
                                    >
                                        Edit
                                    </button>

                                    @if($u->id !== auth()->id())
                                        <button
                                            type="button"
                                            class="rounded-lg bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700"
                                            @click="openDelete({{ $u->id }})"
                                        >
                                            Delete
                                        </button>
                                    @else
                                        <span class="text-xs text-gray-400">(you)</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                No users found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>
        {{ $users->links() }}
    </div>

    {{-- Add modal --}}
    <x-modal show="addOpen" title="Add User">
        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-3">
            @csrf

            <div>
                <label class="text-sm font-medium">Full Name</label>
                <input
                    name="name"
                    x-model="addSingle.name"
                    class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                    required
                    maxlength="100"
                    pattern="[A-Za-zÑñ][A-Za-zÑñ.\-'\s]*"
                    title="Letters only"
                    placeholder="e.g. Juan Dela Cruz"
                >
                <div class="mt-1 text-sm text-red-600" x-show="addSingle.nameError" x-text="addSingle.nameError"></div>
            </div>

            <div>
                <label class="text-sm font-medium">Email</label>
                <input
                    name="email"
                    type="email"
                    x-model="addSingle.email"
                    class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                    required
                    maxlength="255"
                    pattern="[^\s@]+@[^\s@]+\.[^\s@]+"
                    title="Enter a complete email address"
                    placeholder="e.g. juan.delacruz@example.com"
                >
                <div class="mt-1 text-sm text-red-600" x-show="addSingle.emailError" x-text="addSingle.emailError"></div>
            </div>

            <div>
                <label class="text-sm font-medium">Role</label>
                <select
                    name="role"
                    x-model="addSingle.role"
                    class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                    required
                >
                    @foreach($roles as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                <div class="mt-1 text-sm text-red-600" x-show="addSingle.roleError" x-text="addSingle.roleError"></div>
            </div>

            <div>
                <label class="text-sm font-medium">Password</label>
                <input
                    name="password"
                    type="password"
                    x-model="addSingle.password"
                    class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                    required
                    minlength="8"
                >
                <div class="mt-1 text-sm text-red-600" x-show="addSingle.passwordError" x-text="addSingle.passwordError"></div>
            </div>

            <div>
                <label class="text-sm font-medium">Confirm Password</label>
                <input
                    name="password_confirmation"
                    type="password"
                    x-model="addSingle.password_confirmation"
                    class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                    required
                    minlength="8"
                >
            </div>

            <div class="flex gap-2 pt-2">
                <button class="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">Save</button>
                <button type="button" class="rounded-lg bg-gray-100 px-4 py-2 text-gray-700 hover:bg-gray-200" @click="addOpen=false">Cancel</button>
            </div>
        </form>
    </x-modal>

    {{-- Edit modal --}}
    <x-modal show="editOpen" title="Edit User">
        <form
            method="POST"
            :action="`{{ url('/admin/users') }}/${editUser.id}`"
            class="space-y-3"
        >
            @csrf
            @method('PUT')

            <input type="hidden" name="editing_id" :value="editUser.id">

            <div>
                <label class="text-sm font-medium">Full Name</label>
                <input
                    name="name"
                    x-model="editUser.name"
                    class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                    required
                    maxlength="100"
                    pattern="[A-Za-zÑñ][A-Za-zÑñ.\-'\s]*"
                    title="Letters only"
                >
                <div class="mt-1 text-sm text-red-600" x-show="editUser.nameError" x-text="editUser.nameError"></div>
            </div>

            <div>
                <label class="text-sm font-medium">Email</label>
                <input
                    name="email"
                    type="email"
                    x-model="editUser.email"
                    class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                    required
                    maxlength="255"
                    pattern="[^\s@]+@[^\s@]+\.[^\s@]+"
                    title="Enter a complete email address"
                >
                <div class="mt-1 text-sm text-red-600" x-show="editUser.emailError" x-text="editUser.emailError"></div>
            </div>

            <div>
                <label class="text-sm font-medium">Role</label>
                <select
                    name="role"
                    x-model="editUser.role"
                    class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                    required
                >
                    @foreach($roles as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                <div class="mt-1 text-sm text-red-600" x-show="editUser.roleError" x-text="editUser.roleError"></div>
            </div>

            <div>
                <label class="text-sm font-medium">New Password</label>
                <input
                    name="password"
                    type="password"
                    x-model="editUser.password"
                    class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                    minlength="8"
                    placeholder="Leave blank to keep current password"
                >
                <div class="mt-1 text-sm text-red-600" x-show="editUser.passwordError" x-text="editUser.passwordError"></div>
            </div>

            <div>
                <label class="text-sm font-medium">Confirm New Password</label>
                <input
                    name="password_confirmation"
                    type="password"
                    x-model="editUser.password_confirmation"
                    class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                    minlength="8"
                >
            </div>

            <div class="flex gap-2 pt-2">
                <button class="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">Update</button>
                <button type="button" class="rounded-lg bg-gray-100 px-4 py-2 text-gray-700 hover:bg-gray-200" @click="editOpen=false">Cancel</button>
            </div>
        </form>
    </x-modal>

    {{-- Delete modal --}}
    <x-modal show="deleteOpen" title="Delete User">
        <div class="space-y-3">
            <div class="text-sm text-gray-700">
                Are you sure you want to delete this user account?
            </div>

            <form
                method="POST"
                :action="`{{ url('/admin/users') }}/${deleteUserId}`"
                @submit="if (!deleteUserId) $event.preventDefault()"
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
