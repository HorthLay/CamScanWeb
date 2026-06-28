<?php

namespace App\Http\Controllers\Auth;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;    
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request){
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],   
        ]);


        $user = User::where('name',$request->name)->first();

        if(! $user){
            return back()
            ->withErrors(['name' => 'The provided credentials do not match our records.'])
            ->withInput();
        }

        if(! $user->active){
            return back()
            ->withErrors(['name' => 'Your account is not active. Please contact the administrator.'])
            ->withInput();
        }

        if(! Auth::attempt($request->only('name','password'))){
            return back()
            ->withErrors(['name' => 'The provided credentials do not match our records.'])
            ->withInput();
        }

        $request->session()->regenerate();



        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
