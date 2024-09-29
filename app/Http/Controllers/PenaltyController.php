<?php

namespace App\Http\Controllers;

use App\Models\Penalty;
use Illuminate\Http\Request;

class PenaltyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $penalties = Penalty::all();
        return view('penalties.index', compact('penalties'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('penalties.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'violation' => 'required|string|max:255',
            'penalty_type' => 'required|in:fixed,conditional',
            'penalty_hours' => 'required_if:penalty_type,fixed|nullable|integer|min:1',
            'conditions' => 'required_if:penalty_type,conditional|nullable|string|max:255',
        ]);

        Penalty::create($request->all());

        return redirect()->route('penalties.index')->with('success', 'Penalty created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $penalty = Penalty::findOrFail($id);
        return view('penalties.show', compact('penalty'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $penalty = Penalty::findOrFail($id);
        return view('penalties.edit', compact('penalty'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'violation' => 'required|string|max:255',
            'penalty_type' => 'required|in:fixed,conditional',
            'penalty_hours' => 'required_if:penalty_type,fixed|nullable|integer|min:1',
            'conditions' => 'required_if:penalty_type,conditional|nullable|string|max:255',
        ]);
        
        $penalty = Penalty::findOrFail($id);
        $penalty->update($request->all());

        return redirect()->route('penalties.index')->with('success', 'Penalty updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $penalty = Penalty::findOrFail($id);
        $penalty->delete();

        return redirect()->route('penalties.index')->with('success', 'Penalty deleted successfully.');
    }
}
