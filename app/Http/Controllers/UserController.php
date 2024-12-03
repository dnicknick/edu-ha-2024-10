<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        // Валидация и создание пользователя
        //$validator = Validator::make($request->all(), [
        //    'first_name' => 'required|string|max:255',
        //    'second_name' => 'required|string|max:255',
        //    'birthdate' => 'required|string|max:255',
        //    'biography' => 'required|string|max:255',
        //    'city' => 'required|string|max:255',
        //    'email' => 'required|string|email|max:255|unique:users',
        //    'password' => 'required|string|min:8',
        //]);

        $birthdate = new \DateTimeImmutable($request->get('birthdate', ''));
        $password = $request->get('password', '');
        $hash = password_hash($password, PASSWORD_BCRYPT);

        $user = User::create([
            'first_name' => $request->get('first_name', ''),
            'second_name' => $request->get('second_name', ''),
            'birthdate' => $birthdate->format('Y.m.d'),
            'biography' => $request->get('biography', ''),
            'city' => $request->get('city', ''),
            'email' => $request->get('email', ''),
            'password' => $hash,
            'remember_token' => $hash,
        ]);

        // 200
        // user_id "e4d2e6b0-cde2-42c5-aac3-0b8316f21e58"

        // "400": { "description": "Невалидные данные" },
        // "500": { "$ref": "#/components/responses/5xx"},
        // "503": { "$ref": "#/components/responses/5xx"}
        $userId = sprintf('%s-%s', 'user', $user->getKey());
        return response()->json(['user_id' => $userId], 200);
    }

    public function login(Request $request): JsonResponse
    {
        // id + password

        // Логика авторизации
        //$credentials = $request->only('email', 'password');

        //if (Auth::attempt($credentials)) {
        //    return response()->json(Auth::user(), 200);
        //}
        // 200
        // "description": "Успешная аутентификация"
        // token
        // "example": "e4d2e6b0-cde2-42c5-aac3-0b8316f21e58"

        // 400 "Невалидные данные"
        // 404 "Пользователь не найден"
        // 500 "5xx"
        // 503 5xx

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function getUser($id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);
            return response()->json($user);
        } catch (\Exception $e) {
            return response()->json([], 404);
        }
    }

    public function searchUsers(Request $request): JsonResponse
    {
        $firstName = $request->input('first_name');
        $lastName = $request->input('last_name');

        $users = [];
        if ($firstName !== null) {
            $users = User::where('first_name', 'LIKE', "%{$firstName}%")->get();
        }
        // по имени или фамилии
        if ($lastName !== null) {
            $users = User::where('second_name', 'LIKE', "%{$lastName}%")->get();
        }

        if (($firstName !== null) && ($lastName !== null)) {
            $users = User::query()
                ->where('first_name', 'LIKE', "%{$firstName}%")
                ->orWhere('second_name', 'LIKE', "%{$lastName}%")
                ->get();
        }

        return response()->json(['items' => $users], 200);
    }
}
