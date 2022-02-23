<?php

namespace App\Http\Controllers;

use App\Http\Resources\ImageResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    private AuthService $authService;

    /**
     * @param AuthService $authService
     */
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     * @throws \App\Exceptions\NotFoundResourceException
     */
    public function login(Request $request): JsonResponse
    {
        return response()->json([
            'token' => $this->authService->login($request->all())
        ]);
    }

    public function logout(Request $request): bool
    {
        $this->authService->logout($request);
        return true;
    }
}
