<?php

namespace App\Services\Auth;

use App\Domains\Auth\AuthRepositoryInterface;
use App\Domains\Auth\Entities\RegisterUserEntity;
use App\Enums\UserRoleEnum;
use App\Exceptions\Commons\BadRequestException;
use App\Exceptions\Commons\UnauthorizedException;
use App\Models\User;
use App\Services\Auth\Entities\LoginEntity;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;
use Mockery\MockInterface;

class AuthService implements AuthServiceInterface
{
    private AuthRepositoryInterface|MockInterface $_authRepository;
    public function __construct(
        AuthRepositoryInterface|MockInterface $authRepository
    ) {
        $this->_authRepository = $authRepository;
    }

    function login(LoginEntity $params): string
    {
        //* get user
        $user = $this->_authRepository->getUser($params->email);

        //* check user
        if (! $user || ! Hash::check($params->password, $user->password)) {
            throw new BadRequestException('The provided credentials are incorrect.');
        }

        //* generate & return token
        $accessToken = $user->createToken($params->device_name)->plainTextToken;
        return $accessToken;
    }

    public function logout(): void
    {
        $loggedUser = auth('sanctum')->user();
        $loggedUser->tokens()->delete();
    }

    function getLoggedUser(): User
    {
        $loggedUser = auth('sanctum')->user();
        return $loggedUser;
    }

    public function getListUsers(User $loggedUser): Collection
    {
        //* check logged user is super admin
        if ($loggedUser->role !== UserRoleEnum::SUPER_ADMIN) {
            throw new UnauthorizedException('action is unauthorized, only for super admin');
        }

        //* get list users
        $users = $this->_authRepository->getListUsers();

        return $users;
    }

    function register(User $loggedUser, object $validatedRequest): User
    {
        //* check logged user is super admin
        if ($loggedUser->role !== UserRoleEnum::SUPER_ADMIN) {
            throw new UnauthorizedException('action is unauthorized, only for super admin');
        }

        //* register user
        $registerUserEntity = new RegisterUserEntity($validatedRequest);
        $registeredUser = $this->_authRepository->registerUser($registerUserEntity);

        return $registeredUser;
    }
}
