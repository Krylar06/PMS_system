<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Office;
use App\Models\Staff;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index(Office $office)
    {
        $staff = Staff::where('office_id', $office->id)
            ->orderBy('last_name')->orderBy('first_name')
            ->paginate(15);

        $office->load('college');

        return view('admin.staff.index', compact('office', 'staff'));
    }

    public function store(Request $request, Office $office)
    {
        // Single add OR bulk add (array of staff rows: staff[0][first_name], etc.)
        $isBulk = $request->has('staff');

        if ($isBulk) {
            $rows = $request->input('staff', []);
            $count = min(max(count($rows), 0), 3);

            $rules = [];
            for ($i = 0; $i < $count; $i++) {
                $rules["staff.$i.first_name"] = ['required', 'string', 'max:255'];
                $rules["staff.$i.last_name"] = ['required', 'string', 'max:255'];
                $rules["staff.$i.position"] = ['nullable', 'string', 'max:255'];
                $rules["staff.$i.email"] = ['nullable', 'email', 'max:255'];
                $rules["staff.$i.phone"] = ['nullable', 'string', 'max:255'];
                $rules["staff.$i.is_active"] = ['sometimes', 'boolean'];
            }

            $data = $request->validateWithBag('add', $rules, [
                'staff.*.first_name.required' => 'First name is required.',
                'staff.*.last_name.required' => 'Last name is required.',
                'staff.*.email.email' => 'Please enter a valid email address.',
            ], [
                'staff.*.first_name' => 'first name',
                'staff.*.last_name' => 'last name',
                'staff.*.position' => 'position',
                'staff.*.email' => 'email',
                'staff.*.phone' => 'phone',
            ]);

            foreach ($data['staff'] as $row) {
                Staff::create([
                    'office_id' => $office->id,
                    'first_name' => $row['first_name'],
                    'last_name' => $row['last_name'],
                    'position' => $row['position'] ?? null,
                    'email' => $row['email'] ?? null,
                    'phone' => $row['phone'] ?? null,
                    'is_active' => (bool) ($row['is_active'] ?? true),
                ]);
            }

            return back()->with('success', 'Staff created.');
        }

        // Single
        $data = $request->validateWithBag('add', [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        Staff::create([
            'office_id' => $office->id,
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'position' => $data['position'] ?? null,
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'is_active' => (bool) ($data['is_active'] ?? true),
        ]);

        return back()->with('success', 'Staff created.');
    }

    public function edit(Office $office, Staff $staff)
    {
        abort_unless($staff->office_id === $office->id, 404);
        $office->load('college');

        return view('admin.staff.edit', compact('office', 'staff'));
    }

    public function update(Request $request, Office $office, Staff $staff)
    {
        abort_unless($staff->office_id === $office->id, 404);

        $data = $request->validateWithBag('edit', [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $staff->update([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'position' => $data['position'] ?? null,
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'is_active' => (bool) ($data['is_active'] ?? false),
        ]);

        $office->load('college');

        return redirect()->route('admin.staff.index', $office)->with('success', 'Staff updated.');
    }

    public function destroy(Office $office, Staff $staff)
    {
        abort_unless($staff->office_id === $office->id, 404);
        $staff->delete();
        return back()->with('success', 'Staff deleted.');
    }
}