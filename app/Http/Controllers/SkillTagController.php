<?php

namespace App\Http\Controllers;

use App\Models\SkillTag;
use Illuminate\Http\Request;

class SKillTagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $skillTags = SkillTag::all();
        return view('skill_tags.index', compact('skillTags'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('skill_tags.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:skill_tags,name',
        ]);
    
        SkillTag::create([
            'name' => $request->name,
        ]);
    
        return redirect()->route('skill_tags.index')->with('success', 'Skill tag added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return view('skill_tags.edit', compact('skillTag'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SkillTag $skillTag)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:skill_tags,name,' . $skillTag->id,
        ]);
    
        $skillTag->update([
            'name' => $request->name,
        ]);
    
        return redirect()->route('skill_tags.index')->with('success', 'Skill tag updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $skillTag->delete();
        return redirect()->route('skill_tags.index')->with('success', 'Skill Tag deleted successfully.');
    }
}
