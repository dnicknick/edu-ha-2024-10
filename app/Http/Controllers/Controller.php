<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    public function getAuth(Request $request): ?User
    {
        /** @var ?User $user */
        $user = User::query()
            ->where(['remember_token' => $request->bearerToken()])
            ->first();

        return $user;
    }
}
