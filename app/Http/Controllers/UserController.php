<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        try {
            $this->validate($request, [
                'first_name' => 'required|string|max:255',
                'second_name' => 'required|string|max:255',
                'birthdate' => 'required|string|max:255',
                'biography' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
            ]);

            $birthdate = new \DateTimeImmutable(
                $request->get('birthdate', '')
            );
            $user = User::create([
                'first_name' => $request->get('first_name', ''),
                'second_name' => $request->get('second_name', ''),
                'birthdate' => $birthdate->format('Y.m.d'),
                'biography' => $request->get('biography', ''),
                'city' => $request->get('city', ''),
                'email' => $request->get('email', ''),
                'password' => password_hash(
                    $request->get('password', ''),
                    PASSWORD_BCRYPT
                ),
                'remember_token' => $this->generateUUIDv4Manual(),
            ]);

            return $this->httpJsonOk([
                'user_id' => $user->remember_token
            ]);
        } catch (ValidationException $e) {
            return $this->httpJsonBadRequest([
                'description' => 'Невалидные данные',
                'message' => $e->getMessage(),
            ]);
        } catch (\Exception $e) {
            return $this->httpJsonInternalServerError([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function login(Request $request): JsonResponse
    {
        try {
            $this->validate($request, [
                'id' => 'required|string|max:255',
                'password' => 'required|string|min:8',
            ]);

            $userId = $request->get('id', null);
            $user = User::find($userId);
            if ($user === null) {
                return $this->httpJsonNotFound([
                    'description' => 'Пользователь не найден',
                ]);
            }

            $userPassword = $request->get('password', null);
            if (!password_verify($userPassword, $user->password)) {
                return $this->httpJsonNotFound([
                    'description' => 'Пароль неправильный',
                ]);
            }

            return $this->httpJsonOk(['token' => $user->remember_token]);
        } catch (ValidationException $e) {
            return $this->httpJsonBadRequest([
                'description' => 'Невалидные данные',
                'message' => $e->getMessage(),
            ]);
        } catch (\Exception $e) {
            return $this->httpJsonInternalServerError([
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getUser($id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);
            return $this->httpJsonOk($user);
        } catch (\Exception $e) {
            return $this->httpJsonNotFound([]);
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

    private function generateUUIDv4Manual(): string
    {
        $hex = bin2hex(random_bytes(16));
        return sprintf('%s-%s-%s-%s-%s',
            substr($hex, 0, 8),
            substr($hex, 8, 4),
            '4' . substr($hex, 13, 3), // Устанавливаем версию 4
            substr($hex, 16, 4),
            substr($hex, 20, 12)
        );
    }

    public function info(Request $request): JsonResponse
    {
        try {
            $user = $this->getAuth($request);
            return $this->httpJsonOk($user);
        } catch (\Exception $e) {
            return $this->httpJsonNotFound([]);
        }
    }
}
