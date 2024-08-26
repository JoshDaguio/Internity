<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

use Illuminate\Http\Request;

class CompanyController extends Controller
{

    public function dashboard()
    {
        // You can pass any necessary data to the dashboard view
        return view('company.dashboard');
    }
    public function index()
    {
        $companies = User::where('role_id', 4)->get();

        return view('company.index', compact('companies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
        ]);

        // Create a new company user in the 'users' table
        $company = new User();
        $company->name = $request->name;
        $company->email = $request->email;
        $company->password = Hash::make($request->password); // Store hashed password
        $company->role_id = 4; // Set role_id to 4 for company accounts
        $company->status_id = 1; // Assuming 1 is the default active status in the 'account_statuses' table
        $company->save();

        return redirect()->route('company.index')->with('success', 'Company account created successfully.');
    }

    public function create()
    {
        return view('company.create');
    }
    
    public function show(User $company)
    {
        // Ensure we're displaying a company account (role_id = 4)
        if ($company->role_id !== 4) {
            abort(404);
        }

        return view('company.show', compact('company'));
    }

    public function edit($id)
    {
        $company = User::where('role_id', 4)->findOrFail($id);
        return view('company.edit', compact('company'));
    }

    public function update(Request $request, User $company)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $company->id,
            'password' => 'nullable|string|min:8',
        ]);

        //Update Company's information in 'User' Table
        $company->name = $request->name;
        $company->email = $request->email;
        if ($request->filled('password')){
            $company->password = Hash::make($request->password);
        }  
        $company->save();

        return redirect()->route('company.index')->with('success', 'Company Updated Successfully!');
    }

    public function destroy(User $company)
    {
        if ($company->role_id !==4){
            abort(404);
        }

        $company->delete();

        return redirect()->route('company.index')->with('success', 'Company deleted successfully!');
    }

}
