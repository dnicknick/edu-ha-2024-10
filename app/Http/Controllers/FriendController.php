<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FriendController extends Controller
{
    public function addFriend($user_id, Request $request): JsonResponse
    {
        try {
            $friend = User::findOrFail($user_id);
            $authUser = $this->getAuth($request);
            if ($authUser === null) {
                return response()->json([], 401); // 401 Unauthorized
            }
            $authUser->friends()->attach($friend);
            return response()->json([
                'message' => 'Пользователь успешно указал своего друга'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([], 400); // 400, 401, 500, 503
        }
    }

    public function removeFriend($user_id, Request $request): JsonResponse
    {
        try {
            $friend = User::findOrFail($user_id);
            $authUser = $this->getAuth($request);
            if ($authUser === null) {
                return response()->json([], 401); // 401 Unauthorized
            }
            if ($authUser->friends()->count() === 0) {
                return response()->json([], 400);
            }
            //if ($authUser->friends()->contains($friend) === false) {
            //    return response()->json([], 400);
            //}
            $authUser->friends()->detach($friend);

            return response()->json([
                'message' => __('Пользователь успешно удалил из друзей пользователя')
            ], 200);
        } catch (\Exception $e) {
            return response()->json([], 400); // 400, 401, 500, 503
        }
    }
}
