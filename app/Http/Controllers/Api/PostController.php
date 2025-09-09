<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DragonCode\PrettyArray\Services\Formatters\Json;
use Illuminate\Http\JsonResponse;

class PostController extends Controller
{
    /**
     * Affiche une liste des posts.
     */

    public function index(): JsonResponse
    {
        $posts = Post::select('id','title','slug','body','active','image','created_at','updated_at')->get();
        return response()->json($posts);
    }
}
