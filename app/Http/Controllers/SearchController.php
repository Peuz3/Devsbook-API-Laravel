<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class SearchController extends Controller
{
    private $loggedUser;

    public function __construct()
    {
        $this->middleware('auth:api');
        $this->loggedUser = auth()->user();
    }

    public function search(Request $request)
    {
        $array = ['error' => '', 'users' => []];

        $txt = $request->input('txt');

        if ($txt) {

            $userList = User::where('name', 'like', '%' . $txt . '%')->get();
            foreach ($userList as $userItem) {
                $array['users'][] = [
                    'id' => $userItem['id'],
                    'name' => $userItem['name'],
                    'avatar' => url('media/avatars/' . $userItem['avatar'])
                ];
            }
        } else {
            $array['error'] = 'Digite alguma coisa para procurar!';
            return $array;
        }

        return $array;
    }
}
