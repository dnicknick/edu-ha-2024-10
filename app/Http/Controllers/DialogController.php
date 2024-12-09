<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Message;

class DialogController extends Controller
{
    public function sendMessage($user_id, Request $request): JsonResponse
    {
        try {
            //$validated = $request->validate(['content' => 'required|string']);
            $user = User::findOrFail($user_id);
            $authUser = $this->getAuth($request);
            if ($authUser === null) {
                return response()->json([], 401); // 401 Unauthorized
            }
            $message = Message::create([
                'sender_id' => $authUser->id,
                'receiver_id' => $user_id,
                'content' => $request->get('text', ''),
            ]);

            return response()->json($message, 200);
        } catch (\Exception $e) {
            return response()->json([], 400); // 400 401 500 503
        }
    }

    public function listDialog($user_id, Request $request): JsonResponse
    {
        try {
            $authUser = $this->getAuth($request);
            if ($authUser === null) {
                return response()->json([], 401); // 401 Unauthorized
            }

            $user = User::findOrFail($user_id);
            $messages = Message::query()
                ->where(function($query) use ($user, $authUser) {
                    $query->where('sender_id', $authUser->id)
                        ->where('receiver_id', $user->id);
                })->orWhere(function($query) use ($user, $authUser) {
                    $query->where('sender_id', $user->id)
                        ->where('receiver_id', $authUser->id);
                })->get();

            return response()->json($messages);
        } catch (\Exception $e) {
            return response()->json([], 400);
        }
    }
}
