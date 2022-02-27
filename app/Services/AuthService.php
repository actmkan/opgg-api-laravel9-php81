<?php

namespace App\Services;

use App\Exceptions\NotFoundResourceException;
use App\Models\Image as ImageModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthService extends Service
{
    /**
     * @param array $attribute
     * @return array
     * @throws \App\Exceptions\NotFoundResourceException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(array $attribute): array
    {
        $validated = $this->validate(
            $attribute,
            [
                'email' => 'required|email',
                'password' => 'required',
            ],
            [
                'email.required' => '이메일은 필수 입력값입니다.',
                'password.required' => '비밀번호는 필수 입력값입니다.',
                'email.email' => '이메일 형식으로 입력해주세요.',
            ]
        );

        if(Auth::attempt($validated)){
            $user = User::where(['email' => $validated['email']])->first();
            $token = $user->createToken('auth-token');

            return [
                'token' => $token->plainTextToken,
                'id' => $user->id,
                'nickname' => $user->nickname,
                'grade_id' => $user->grade_id,
            ];
        }

        throw new NotFoundResourceException();
    }

    public function logout(Request $request): void
    {
        $request->user()->currentAccessToken()->delete();
    }
}
