<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
{

    $validator = Validator::make($request->all(), [
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)
        ->withInput();
    }
    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {
        // return redirect()->intended();
        return redirect()->route('admin.index');
    }else {
        $validator->errors()->add(
            'invalid',
            'Invalid credentials. Please try again'
        );
        return redirect()->back()->withErrors($validator)->withInput();
    }

    // return redirect()->route('admin.show.login')->with('error', 'Invalid credentials. Please try again.');
}

public function logout(Request $request)
{
    Auth::guard("web")->logout();
    return redirect()->route('admin.show.login');
}
}
