<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PersonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $person = Person::orderBy('level', 'asc')
            ->get()
            ->keyBy('id')
            ->toArray();
        $nestedPerson = [];

        foreach ($person as &$eachPerson) {
            if (is_null($eachPerson['parent_id'])) {
                $nestedPerson[] = &$eachPerson;
            } else {
                $parent_id = $eachPerson['parent_id'];

                if (isset($person[$parent_id])) {

                    if (!isset($person[$parent_id]['children'])) {
                        $person[$parent_id]['children'] = [];
                    }

                    $person[$parent_id]['children'][] = &$eachPerson;
                }
            }
        }

        return response()->json(['data' => $nestedPerson]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => [
                'required',
                'string',
            ],
            'gender' => [
                'required',
                Rule::in(Person::GENDERS),
            ],
            'parent_id' => [
                'nullable',
                'exists:person,id',
            ],
        ], [
            'name.required' => 'Nama tidak boleh kosong.',
            'name.string' => 'Nama harus berupa string.',
            'gender.required' => 'Jenis kelamin tidak boleh kosong.',
            'gender.in' => 'Jenis kelamin hanya diperbolehkan 1: Laki-laki, 2: Perempuan',
            'parent_id.exists' => 'Orang tua tidak ditemukan.',
        ]);

        $level = 1;

        if ($request->filled('parent_id')) {
            $parent = Person::find($request->parent_id);

            $level = $parent->level + 1;
        }

        $person = Person::create($request->only([
            'name',
            'gender',
            'parent_id',
        ]) + ['level' => $level]);

        return response()->json(['data' => $person], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json(['data' => Person::findOrFail($id)]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $person = Person::findOrFail($id);
        $parent = null;
        $level = $person->level;

        if ($request->filled('parent_id')) {
            $parent = Person::find($request->parent_id);

            if (empty($parent)) {
                throw ValidationException::withMessages(['parent_id' => 'Orang tua tidak ditemukan.']);
            }

            $level = $parent->level + 1;
        }

        $this->validate($request, [
            'name' => [
                'required',
                'string',
            ],
            'gender' => [
                'required',
                Rule::in(Person::GENDERS),
            ],
            'parent_id' => [
                'nullable',
                function ($attribute, $value, $fail) use ($person, $parent) {
                    if (!empty($parent)) {
                        if ($parent->level != ($person->level - 1)) {
                            $fail('Hanya diperbolehkan mengubah orang tua.');
                        }
                    }
                },
            ],
        ], [
            'name.required' => 'Nama tidak boleh kosong.',
            'name.string' => 'Nama harus berupa string.',
            'gender.required' => 'Jenis kelamin tidak boleh kosong.',
            'gender.in' => 'Jenis kelamin hanya diperbolehkan 1: Laki-laki, 2: Perempuan',
            'parent_id.exists' => 'Orang tua tidak ditemukan.',
        ]);

        $person->update($request->only([
            'name',
            'gender',
            'parent_id',
        ]) + ['level' => $level]);

        return response()->json(['data' => $person], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $person = Person::findOrFail($id);

        $children = Person::where('parent_id', $person->id)->get();

        if ($children->count()) {
            return response()->json(['message' => 'Orang mempunyai anak, mohon hapus anak terlebih dahulu.'], 403);
        }

        $person->delete();

        return response(null, 204);
    }
}
