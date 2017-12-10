<?php

namespace App\Http\Controllers\Auth;

use App\Mail\VerifyMail;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Models\VerifyUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
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
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        /*
         * Cria o usuario.
         * OBS: nao passamos o campo verified pois ele por default e false e eh
         * o que realmente queremos
         */
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
        // criamos o token de verificacao vinculado com o usuario
        VerifyUser::create([
            'user_id' => $user->id,
            'token'    => str_random(40)
        ]);
        // envia o email para o usuario criado usando no send() a classe VerifyMAil()
        Mail::to($user->email)->send(new VerifyMail($user));
        return $user;
    }

    /**
     * Verifica o status do usuario.
     *
     * @param $token
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verifyUser($token)
    {
        // verifica no banco se exste algum token em verify_users_table
        $verified = VerifyUser::where('token', $token)->first();
        // verifica se localizou um verified
        if( isset($verified) )
        {
            // pega o usuario
            $user = $verified->user;
            // verifica se ele nao esta verificado
            if(!$user->verified)
            {
                // atualiza e salva o novo status no user
                $verified->user->verified = true;
                $verified->user->save();
                $status = 'E-mail verificado com sucesso';
            }
            else
            {
                $status = 'Este e-mail já está verificado';
            }
        }
        else
        {
            return redirect('/login')->with('warning', 'Este e-mail não pode ser verificado');
        }
        return redirect('/login')->with('status', $status);
    }

    /**
     * Sobreposicao do metodo registered() da Trait RegistersUsers
     * Logo apos o registro este metodo eh chamado para executar alguma logica.
     * Desloga o usuario e o redireciona para /login para verificar seu email
     *
     * @param Request $request
     * @param $user
     */
    public function registered(Request $request, $user)
    {
        auth()->logout();
        return redirect('/login')->with('status', 'Enviamos um código de verificação para você, por favor, verifique seu email.');
    }

}
