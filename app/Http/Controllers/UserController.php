<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use App\Models\User;
use App\Models\UserRelation;
use Intervention\Image\Facades\Image;


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
        if ($name) {
            $user->name = $name;
        }

        //E-MAIL
        if ($email) {
            if ($email != $user->email) {
                $emailExists = User::where('email', $email)->count();
                if ($emailExists === 0) {
                    $user->email = $email;
                } else {
                    $array['error'] = 'E-mail já existe!';
                    return $array;
                }
            }
        }

        //BIRTHDATE
        if ($birthdate) {
            if (strtotime($birthdate) === false) {
                $array['error'] = 'Data de nascimento inválida!';
                return $array;
            }

            $user->birthdate = $birthdate;
        }

        //CITY
        if ($city) {
            $user->city = $city;
        }

        //WORK
        if ($work) {
            $user->work = $work;
        }

        //PASSWORD
        if ($password && $password_confirm) {
            if ($password === $password_confirm) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $user->password = $hash;
            } else {
                $array['error'] = 'As senhas são distintas!';
            }
        }

        $user->save();

        return $array;
    }

    public function updateAvatar(Request $request)
    {
        $array = ['error' => ''];
        $allowedTypes = ['image/jpg', 'image/jpeg', 'image/png'];

        $image = $request->file('avatar');

        if ($image) {
            if (in_array($image->getClientMimeType(), $allowedTypes)) {
                //Gerando um nome randômico para a imagem
                $filename = md5(time() . rand(0, 999)) . '.jpg';

                $destPath = public_path('/media/avatars');

                $img = Image::make($image->path())
                    ->fit(200, 200)
                    ->save($destPath . '/' . $filename);

                $user = User::find($this->loggedUser['id']);
                $user->avatar = $filename;
                $user->save();

                $array['url'] = url('/media/avatars/' . $filename);
            } else {
                $array['error'] = 'Arquivo não suportado!';
                return $array;
            }
        } else {
            $array['error'] = 'Arquivo não enviado!';
            return $array;
        }

        return $array;
    }

    public function updateCover(Request $request)
    {
        $array = ['error' => ''];
        $allowedTypes = ['image/jpg', 'image/jpeg', 'image/png'];

        $image = $request->file('cover');

        if ($image) {
            if (in_array($image->getClientMimeType(), $allowedTypes)) {
                //Gerando um nome randômico para a imagem
                $filename = md5(time() . rand(0, 999)) . '.jpg';

                $destPath = public_path('/media/covers');

                $img = Image::make($image->path())
                    ->fit(850, 310)
                    ->save($destPath . '/' . $filename);

                $user = User::find($this->loggedUser['id']);
                $user->cover = $filename;
                $user->save();

                $array['url'] = url('/media/avatars/' . $filename);
            } else {
                $array['error'] = 'Arquivo não suportado!';
                return $array;
            }
        } else {
            $array['error'] = 'Arquivo não enviado!';
            return $array;
        }

        return $array;
    }

    public function read($id = false)
    {
        $array = ['error' => ''];

        if ($id) {
            $info = User::find($id);
            if (!$info) {
                $array['error'] = 'Usuário Inexistente!';
                return $array;
            }
        } else {
            $info = $this->loggedUser;
        }

        $info['avatar'] = url('media/avatars/' . $info['avatar']);
        $info['cover'] = url('media/covers/' . $info['cover']);

        $info['myUser'] = ($info['id'] == $this->loggedUser['id']) ? true : false;

        $dateFrom = new \DateTime($info['birthdate']);
        $dateTo = new \DateTime('today');
        $info['age'] = $dateFrom->diff($dateTo)->y;

        $info['followers']  = UserRelation::where('user_to', $info['id'])->count();
        $info['following']  = UserRelation::where('user_from', $info['id'])->count();

        $info['photoCount'] = Post::where('id_user', $info['id'])
            ->where('type', 'photo')
            ->count();

        $hasRelation = UserRelation::where('user_from', $this->loggedUser['id'])
            ->where('user_to', $info['id'])
            ->count();
        $info['isFollowing'] = ($hasRelation > 0) ? true : false;

        $array['data'] = $info;

        return $array;
    }

    public function follow($id)
    {
        $array = ['error' => ''];

        if ($id == $this->loggedUser['id']) {
            $array['error'] = 'Você não pode seguir a si mesmo(a)!';
            return $array;
        }

        $userExists = User::find($id);
        if ($userExists) {
            $relation = UserRelation::where('user_from', $this->loggedUser['id'])
                ->where('user_to', $id)
                ->first();

            if ($relation) {
                //Para de seguir
                $relation->delete();
            } else {
                //Começa a seguir
                $newRelation = new UserRelation();
                $newRelation->user_from = $this->loggedUser['id'];
                $newRelation->user_to = $id;
                $newRelation->save();
            }
        } else {
            $array['error'] = 'Usuário inexistente!';
            return $array;
        }

        return $array;
    }
}
