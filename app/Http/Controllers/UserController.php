<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UserController extends Controller
{
    private $loggedUser;
    
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->loggedUser = Auth::user(); //auth()->user();
    }

    public function update(Request $request)
    {
        $array = ['error' => ''];

        $name = $request->input('name');
        $email = $request->input('email');
        $birthdate = $request->input('birthdate');
        $city = $request->input('city');
        $work = $request->input('work');
        $password = $request->input('password');
        $password_confirm = $request->input('password_confirm');

        $user = User::find($this->loggedUser['id']);

        //Fazendo a checagem item a item

        //NAME
        if($name)
        {
            $user->name = $name;
            
        }

        //E-MAIL
        if($email)
        {
            if($email != $user->email)
            {
                $emailExists = User::where('email', $email)->count();
                if($emailExists === 0)
                {
                    $user->email = $email;
                }
                else
                {
                    $array['error'] = 'E-mail já existe!';
                    return $array;
                }
            }
        }

        //BIRTHDATE
        if($birthdate)
        {
            if(strtotime($birthdate) === false)
            {
                $array['error'] = 'Data de nascimento inválida!';
                return $array;
            }

            $user->birthdate = $birthdate;
        }

        //CITY
        if($city)
        {
            $user->city = $city;
        }

        //WORK
        if($work)
        {
            $user->work = $work;
        }

        //PASSWORD
        if($password && $password_confirm)
        {
            if($password === $password_confirm)
            {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $user->password = $hash;
            }
            else
            {
                $array['error'] = 'As senhas são distintas!';
            }
        }

        $user->save();

        return $array;
    }
}
