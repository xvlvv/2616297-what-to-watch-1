<?php

namespace App\Services;

use App\Data\LoginUserData;
use App\Data\RegisterUserData;
use App\Exceptions\InvalidCredentialsException;
use App\Models\User;
use App\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\NewAccessToken;

readonly class AuthService
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {
    }

    public function login(LoginUserData $DTO): NewAccessToken
    {
        if (false === Auth::attempt(
                [
                    'email' => $DTO->email,
                    'password' => $DTO->password,
                ]
            )) {
            throw new InvalidCredentialsException();
        }

        return Auth::user()->createToken(env('APP_NAME', 'Laravel'));
    }

    public function register(RegisterUserData $DTO): NewAccessToken
    {
        $id = $this->userRepository->create(
            $DTO
        );

        return User::find($id)->createToken(env('APP_NAME', 'Laravel'));
    }
}