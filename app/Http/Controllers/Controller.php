<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use Symfony\Component\HttpFoundation\Response;

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

    public function httpJsonOk($data): JsonResponse
    {
        return response()->json(
            $data,
            Response::HTTP_OK
        );
    }

    public function httpJsonNotFound($data): JsonResponse
    {
        return response()->json(
            $data,
            Response::HTTP_NOT_FOUND
        );
    }

    public function httpJsonBadRequest($data): JsonResponse
    {
        return response()->json(
            $data,
            Response::HTTP_BAD_REQUEST
        );
    }

    public function httpJsonInternalServerError($data): JsonResponse
    {
        return response()->json(
            $data,
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }
}
