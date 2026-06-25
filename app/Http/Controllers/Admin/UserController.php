<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    private const NAME_REGEX = '/^[A-Za-zÑñ][A-Za-zÑñ.\-\'\s]*$/u';

    public function index()
    {
        $users = User::orderBy('name')->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $data = $request->validateWithBag('add', [
            'name' => ['required', 'string', 'max:100', 'regex:' . self::NAME_REGEX],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in(array_keys(User::ROLES))],
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'name.regex' => 'Please enter a valid name (letters only).',
            'email.unique' => 'This email is already registered.',
        ]);

        $newUser = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'password' => Hash::make($data['password']),
        ]);

        ActivityLog::record('created', "Created user account \"{$newUser->name}\" ({$newUser->roleLabel()})", $newUser);

        return back()->with('success', 'User created.');
    }

    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => ['required', 'string', 'max:100', 'regex:' . self::NAME_REGEX],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', Rule::in(array_keys(User::ROLES))],
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ];

        $data = $request->validateWithBag('edit', $rules, [
            'name.regex' => 'Please enter a valid name (letters only).',
            'email.unique' => 'This email is already registered.',
        ]);

        // Safety net: an admin can't demote themselves away from admin —
        // avoids accidentally locking every admin out of the system.
        if ($user->id === auth()->id() && $user->isAdmin() && $data['role'] !== User::ROLE_ADMIN) {
            return back()->withErrors([
                'role' => 'You cannot remove your own admin role.',
            ], 'edit');
        }

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->role = $data['role'];

        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        ActivityLog::record('updated', "Updated user account \"{$user->name}\" ({$user->roleLabel()})", $user);

        return back()->with('success', 'User updated.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account while logged in as it.');
        }

        $name = $user->name;
        $user->delete();

        ActivityLog::record('deleted', "Deleted user account \"{$name}\"");

        return back()->with('success', 'User deleted.');
    }
}