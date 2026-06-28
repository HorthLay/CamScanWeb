<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(){
        $user = auth()->user()->load('role.tabs');
        $tabs = $user->accessibleTabs();

        return view('dashboard', compact('user', 'tabs'));
    }
}
