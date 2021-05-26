<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', [
            'except' => [
                'login',
                'unauthorized',
                'create'
            ]
        ]);
    }

    public function unauthorized()
    {
        return response()->json(['error' => 'Acesso não Autorizado!'], 401);
    }

    public function login(Request $request)
    {
        $array = ['error' => ''];

        $email = $request->input('email');
        $password = $request->input('password');

        if ($email && $password) {
            $token = Auth::attempt([
                'email' => $email,
                'password' => $password
            ]);

            if (!$token) {
                $array['error'] = 'E-mail e/ou Senha inválido(s)!';
                return $array;
            }

            $array['token'] = $token;

            return $array;
        }

        $array['error'] = 'Dados não enviados!';
        return $array;
    }

    public function logout()
    {
        Auth::logout();
        return ['error' => ''];
    }

    public function refresh()
    {
        $token = Auth::refresh();
        return [
            'error' => '',
            'token' => $token
        ];
    }

    public function create(Request $request)
    {
        $array = ['error' => ''];

        $name = $request->input('name');
        $email = $request->input('email');
        $password = $request->input('password');
        $birthdate = $request->input('birthdate');

        if ($name && $email && $password && $birthdate) {
            //Validando a data
            if (strtotime($birthdate) === false) {
                $array['error'] = "Data de nascimento inválida!";
                return $array;
            }

            //Verificando um e-mail existente
            $emailExists = User::where('email', $email)->count();
            if ($emailExists === 0) {
                $hash = password_hash($password, PASSWORD_DEFAULT);

                $newUser = new User();
                $newUser->name = $name;
                $newUser->email = $email;
                $newUser->password = $hash;
                $newUser->birthdate = $birthdate;
                $newUser->save();

                $token = Auth::attempt([
                    'email' => $email,
                    'password' => $password
                ]);

                if (!$token) {
                    $array['error'] = "Ocorreu um erro!";
                    return $array;
                }

                $array['token'] = $token;
            } else {
                $array['error'] = "E-mail já cadastrado!";
                return $array;
            }
        } else {
            $array['error'] = "Alguns campos não foram enviados!";
            return $array;
        }

        return $array;
    } //FIM DO CREATE
}
