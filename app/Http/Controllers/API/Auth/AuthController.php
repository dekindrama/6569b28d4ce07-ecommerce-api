<?php

namespace App\Http\Controllers\API\Auth;

use App\Enums\UserRoleEnum;
use App\Exceptions\Commons\BadRequestException;
use App\Exceptions\Commons\CommonException;
use App\Exceptions\Commons\UnauthorizedException;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Services\Auth\AuthService;
use App\Services\Auth\AuthServiceInterface;
use App\Services\Auth\Entities\LoginEntity;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(LoginRequest $request, AuthServiceInterface $authService): Response
    {
        try {
            $validatedRequest = (object)$request;
            $loginEntity = new LoginEntity(
                $validatedRequest->email,
                $validatedRequest->password,
                $validatedRequest->device_name,
            );
            $accessToken = $authService->login($loginEntity);

            return ResponseHelper::generate(
                true,
                'success generate token',
                Response::HTTP_OK,
                [
                    'access_token' => $accessToken,
                ],
            );
        } catch (CommonException $th) {
            return $th->renderResponse();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function logout(AuthServiceInterface $authService): Response
    {
        try {
            $authService->logout();

            return ResponseHelper::generate(
                true,
                'success logout',
                Response::HTTP_OK,
            );
        } catch (CommonException $th) {
            return $th->renderResponse();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function getLoggedUser(AuthServiceInterface $authService): Response
    {
        $loggedUser = $authService->getLoggedUser();
        return ResponseHelper::generate(
            true,
            'success get logged user',
            Response::HTTP_OK,
            [
                'logged_user' => $loggedUser,
            ],
        );
    }

    //* only for super admin
    public function getListUsers(AuthServiceInterface $authService): Response
    {
        try {
            //* get list users
            $loggedUser = $authService->getLoggedUser();
            $users = $authService->getListUsers($loggedUser);

            //* response data
            return ResponseHelper::generate(
                true,
                'success get list users',
                Response::HTTP_OK,
                [
                    'users' => $users,
                ],
            );
        } catch (CommonException $th) {
            return $th->renderResponse();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    //* only for super admin
    public function register(RegisterRequest $request, AuthServiceInterface $authService): Response
    {
        try {
            $loggedUser = $authService->getLoggedUser();
            $validatedRequest = (object)$request;
            $registeredUser = $authService->register($loggedUser, $validatedRequest);

            //* response data
            return ResponseHelper::generate(
                true,
                'success register user',
                Response::HTTP_CREATED,
                [
                    'user' => $registeredUser,
                ],
            );
        } catch (CommonException $th) {
            return $th->renderResponse();
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
