<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Timetable;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GroupController extends Controller
{
    public function index()
    {
        return view('master.index', [
            'title' => 'Groups',
            'route' => 'groups',
            'columns' => ['name' => 'Group', 'part' => 'Part'],
            'rows' => Group::orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return $this->form();
    }

    public function store(Request $request)
    {
        Group::create($request->validate($this->rules()));

        return redirect()->route('groups.index')->with('success', 'Group created.');
    }

    public function edit(Group $group)
    {
        return $this->form($group);
    }

    public function update(Request $request, Group $group)
    {
        $group->update($request->validate($this->rules($group)));

        return redirect()->route('groups.index')->with('success', 'Group updated.');
    }

    public function destroy(Group $group)
    {
        if (Timetable::where('group_id', $group->id)->exists()) {
            return back()->with('error', 'Group is used by a timetable entry.');
        }

        $group->delete();

        return redirect()->route('groups.index')->with('success', 'Group deleted.');
    }

    private function rules(?Group $group = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('groups', 'name')->ignore($group)],
            'part' => ['nullable', 'string', 'max:10'],
        ];
    }

    private function form(?Group $group = null)
    {
        return view('master.form', [
            'title' => $group ? 'Edit group' : 'New group',
            'route' => 'groups',
            'action' => $group ? route('groups.update', $group) : route('groups.store'),
            'method' => $group ? 'PUT' : 'POST',
            'record' => $group,
            'fields' => [
                ['name' => 'name', 'label' => 'Group name', 'required' => true],
                ['name' => 'part', 'label' => 'Part'],
            ],
        ]);
    }
}