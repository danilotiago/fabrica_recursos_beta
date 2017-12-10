<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

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
    protected $redirectTo = '/home';

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
     * Sobreposicao do metodo authenticated() da Trait AuthenticatesUsers
     * Executa a logica de verificacao do usuario para autentica-lo.
     * Verifica o atributo verified do usuario.
     *
     * @param Request $request
     * @param $user
     */
    public function authenticated(Request $request, $user)
    {
        if(!$user->verified)
        {
            auth()->logout();
            return back()->with('warning', 'Verifique sua conta de e-mail e clique no link de confirmação de e-mail enviado por nós.');
        }
        return redirect()->intended($this->redirectPath());
    }
}
