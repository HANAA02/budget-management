<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    
    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'email';
    }
    
    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        // Adapter les noms de champs selon votre base de données
        return [
            'email' => $request->email,
            'password' => $request->password,
        ];
    }
    
    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
// Dans app/Http/Controllers/Auth/LoginController.php
protected function authenticated(Request $request, $user)
{
    // Mettre à jour la date de dernière connexion
    $user->update([
        'dernier_login' => now(),
    ]);
    
    // Rediriger l'utilisateur en fonction de son rôle
    if ($user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    
    return redirect()->route('user.dashboard');
}
    
    /**
     * Handle a login request to the application.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    // public function login(LoginRequest $request)
    // {
    //     // Tentative de connexion
    //     if (Auth::attempt([
    //         'email' => $request->email,
    //         'password' => $request->password,
    //     ], $request->filled('remember'))) {
            
    //         $request->session()->regenerate();
            
    //         $user = Auth::user();
    //         $user->update(['dernier_login' => now()]);
            
    //         if ($user->role === 'admin') {
    //             return redirect()->route('admin.dashboard');
    //         }
            
    //         return redirect()->route('user.dashboard');
    //     }
        
    //     return back()->withErrors([
    //         'email' => 'Les informations de connexion ne correspondent pas à nos enregistrements.',
    //     ])->withInput($request->only('email', 'remember'));
    // }

    public function showLoginForm()
{
    return view('auth.login');
}

public function login(LoginRequest $request)
{
    // Déterminer si l'option "se souvenir de moi" est cochée
    $remember = $request->boolean('remember', false);
    
    if (Auth::attempt($request->only('email', 'password'), $remember)) {
        $request->session()->regenerate();
        
        $user = Auth::user();
        $user->update(['dernier_login' => now()]);
        
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        
        return redirect()->route('user.dashboard');
    }

    return back()->withErrors([
        'email' => 'Les identifiants sont incorrects.',
    ]);
}

public function logout(Request $request)
{
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/login');
}

}