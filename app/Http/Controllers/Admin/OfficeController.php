<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\College;
use App\Models\Office;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OfficeController extends Controller
{
    public function index(College $college)
    {
        $offices = Office::where('college_id', $college->id)->orderBy('name')->paginate(15);
        return view('admin.offices.index', compact('college', 'offices'));
    }

    public function store(Request $request, College $college)
    {
        // Single add OR bulk add (array of names)
        $isBulk = $request->has('names');

        if ($isBulk) {
            $names = $request->input('names', []);
            $count = min(max(count($names), 0), 3);

            $rules = [];
            for ($i = 0; $i < $count; $i++) {
                $rules["names.$i"] = [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('offices', 'name')->where('college_id', $college->id),
                    // Rule::unique only checks the DB, so two identical office
                    // names submitted together in the same batch would both
                    // pass validation and the second insert would throw an
                    // uncaught QueryException. Catch that here instead.
                    function ($attribute, $value, $fail) use ($names, $i) {
                        if ($value === null || $value === '') {
                            return;
                        }

                        foreach ($names as $j => $other) {
                            if ($j !== $i && $other !== null && $other !== '' && $other === $value) {
                                $fail('This office name is used more than once in this submission.');
                                return;
                            }
                        }
                    },
                ];
            }

            $data = $request->validateWithBag('add', $rules, [
                'names.*.required' => 'The office name is required.',
                'names.*.string' => 'The office name must be text.',
                'names.*.max' => 'The office name may not be longer than 255 characters.',
                'names.*.unique' => 'This office name already exists in this college.',
            ], [
                'names.*' => 'office name',
            ]);

            foreach (range(0, $count - 1) as $i) {
                Office::create([
                    'college_id' => $college->id,
                    'name' => $data['names'][$i],
                ]);
            }

            return back()->with('success', 'Offices created.');
        }

        // Single
        $data = $request->validateWithBag('add', [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('offices', 'name')->where('college_id', $college->id),
            ],
        ], [
            'name.unique' => 'This office name already exists in this college.',
        ]);

        Office::create([
            'college_id' => $college->id,
            'name' => $data['name'],
        ]);

        return back()->with('success', 'Office created.');
    }

    public function edit(College $college, Office $office)
    {
        abort_unless($office->college_id === $college->id, 404);
        return view('admin.offices.edit', compact('college', 'office'));
    }

    public function update(Request $request, College $college, Office $office)
    {
        abort_unless($office->college_id === $college->id, 404);

        $data = $request->validateWithBag('edit', [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('offices', 'name')->where('college_id', $college->id)->ignore($office->id),
            ],
        ], [
            'name.unique' => 'This office name already exists in this college.',
        ]);

        $office->update($data);

        return redirect()->route('admin.offices.index', $college)->with('success', 'Office updated.');
    }

    public function destroy(College $college, Office $office)
    {
        abort_unless($office->college_id === $college->id, 404);
        $office->delete();
        return back()->with('success', 'Office deleted.');
    }
}