<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller
{
    public function createPost(Request $request): JsonResponse
    {
        //$validated = $request->validate([
        //    'content' => 'required|string',
        //]);

        $post = Post::create([
            'user_id' => 1, // @fixme auth()->id(),
            'content' => $request->get('text', ''),
        ]);

        return response()->json($post, 200); // Успешно создан пост
    }

    public function updatePost($id, Request $request): JsonResponse
    {
        try {
            $post = Post::findOrFail($id);
            $post->update([
                'content' => $request->get('text', ''),
            ]);

            return response()->json($post, 200); // "Успешно изменен пост"
        } catch (\Exception $e) {
            return response()->json([], 400);// 400 401 500 503
        }
    }

    public function deletePost($id): JsonResponse
    {
        try {
            $post = Post::findOrFail($id);
            $post->delete();
            return response()->json(null, 200); // Успешно удален пост
        } catch (\Exception $e) {
            return response()->json([], 400);// 400 401 500 503
        }
    }

    public function getPost($id): JsonResponse
    {
        try {
            $post = Post::findOrFail($id);
            return response()->json($post);
        } catch (\Exception $e) {
            return response()->json([], 400);
        }
    }

    public function getFriendsFeed(Request $request): JsonResponse
    {
        $offset = $request->get('offset', 0);
        $offset = $offset < 0 ? 0 : $offset;
        $limit = $request->get('limit', 10);
        $limit = $limit < 1 ? 1 : $limit;

        $authUser = User::find(1); //fixme auth()->user();
        if ($authUser === null) {
            return response()->json([], 401);
        }

        $friends = $authUser->friends();
        if ($friends->count() === 0) {
            return response()->json([], 400);
        }

        $friendsIds = $friends->pluck('users.id');
        $posts = Post::whereIn('user_id', $friendsIds);
        if ($offset > 0) {
            $posts = $posts->offset($offset);
        }

        if ($limit > 0) {
            $posts = $posts->limit($limit);
        }
        $posts = $posts->get();

        return response()->json(['items' => $posts], 200);
    }
}
