<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\College;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CollegeController extends Controller
{
    public function index()
    {
        $colleges = College::orderBy('name')->paginate(15);
        return view('admin.colleges.index', compact('colleges'));
    }

    public function create()
    {
        return view('admin.colleges.create');
    }

    public function store(Request $request)
    {
        // Single add OR bulk add (arrays of names/codes)
        // Use has() instead of filled() so bulk mode triggers even if values are empty strings.
        $isBulk = $request->has('names') || $request->has('codes');

        if ($isBulk) {
            $names = $request->input('names', []);
            $codes = $request->input('codes', []);

            $count = max(count($names), count($codes));
            $count = min(max($count, 0), 3);

            $rules = [];
            for ($i = 0; $i < $count; $i++) {
                $rules["names.$i"] = ['required', 'string', 'max:255'];
                $rules["codes.$i"] = [
                    'nullable',
                    'string',
                    'max:255',
                    Rule::unique('colleges', 'code'),
                    // Rule::unique only checks against existing DB rows, so two
                    // duplicate codes submitted together in the same bulk batch
                    // would both pass validation and then blow up with an
                    // uncaught QueryException on the second insert. Catch that
                    // here instead, before it ever reaches the database.
                    function ($attribute, $value, $fail) use ($codes, $i) {
                        if ($value === null || $value === '') {
                            return;
                        }

                        foreach ($codes as $j => $other) {
                            if ($j !== $i && $other !== null && $other !== '' && $other === $value) {
                                $fail('This code is used more than once in this submission.');
                                return;
                            }
                        }
                    },
                ];
            }

            $data = $request->validateWithBag('add', $rules, [
                'names.*.required' => 'The college name is required.',
                'names.*.string' => 'The college name must be text.',
                'names.*.max' => 'The college name may not be longer than 255 characters.',
                'codes.*.string' => 'The code must be text.',
                'codes.*.max' => 'The code may not be longer than 255 characters.',
                'codes.*.unique' => 'This code has already been taken.',
            ], [
                'names.*' => 'college name',
                'codes.*' => 'code',
            ]);


        // If any code is duplicated, Laravel will redirect back with validation errors.
        // We also ensure the message is consistent for both single and bulk modes.


            foreach (range(0, $count - 1) as $i) {
                $code = $data['codes'][$i] ?? null;
                $code = $code === '' ? null : $code;

                College::create([
                    'name' => $data['names'][$i],
                    'code' => $code,
                ]);
            }

            return redirect()->route('admin.colleges.index')->with('success', 'Colleges created.');

        }


        // Single
        $data = $request->validateWithBag('add', [
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('colleges', 'code'),
            ],
        ]);


        $code = $data['code'] ?? null;
        $code = $code === '' ? null : $code;

        College::create([
            'name' => $data['name'],
            'code' => $code,
        ]);

        return redirect()->route('admin.colleges.index')->with('success', 'College created.');
    }

    public function edit(College $college)
    {
        return view('admin.colleges.edit', compact('college'));
    }

    public function update(Request $request, College $college)
    {
        $data = $request->validateWithBag('edit', [
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('colleges', 'code')->ignore($college->id),
            ],
        ]);


        $code = $data['code'] ?? null;
        $code = $code === '' ? null : $code;

        $college->update([
            'name' => $data['name'],
            'code' => $code,
        ]);

        return redirect()->route('admin.colleges.index')->with('success', 'College updated.');
    }

    public function destroy(College $college)
    {
        $college->delete();

        return redirect()->route('admin.colleges.index')->with('success', 'College deleted.');
    }
}