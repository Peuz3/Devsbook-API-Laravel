<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostLike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    private $loggedUser;
    
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->loggedUser = auth()->user();
    }

    public function like($id){
        $array = ['error'=>''];

        // Verificar se o post existe
        $postExists = Post::find($id);
        if($postExists){
            //Verficar se ja houve um like
            $isLiked = PostLike::where('id_post', $id)
            ->where('id_user', $this->loggedUser['id'])
            ->count();

            if($isLiked >0 ){
                //Se sim, remover
                $postLiked = PostLike::where('id_post', $id)
                ->where('id_user', $this->loggedUser['id'])
                ->first();

                $postLiked->delete();

                $array['isLiked'] = false;
            }else{
                //Se não, adicionar
                $newPostLike = new PostLike();
                $newPostLike->id_post = $id;
                $newPostLike->id_user = $this->loggedUser['id'];
                $newPostLike->created_at = date('Y-m-d H:i:s');
                $newPostLike->save();

                $array['isLiked'] = true;
            }

            $likeCount = PostLike::where('id_post', $id)->count();
            $array['likeCount'] = $likeCount;

        }else{
            $array['error'] = "Post Inexistente!";
            return $array;
        }
        return $array;
    }
}
