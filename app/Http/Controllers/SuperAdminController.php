<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SuperAdminController extends Controller
{
    public function index()
    {
        return view('super_admin.dashboard');
        //repeat steps for each role in each Controller
    }
}
