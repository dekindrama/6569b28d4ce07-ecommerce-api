<?php

namespace App\Http\Controllers\API\Auth;

use App\Enums\UserRoleEnum;
use App\Exceptions\Commons\CommonException;
use App\Exceptions\Commons\UnauthorizedException;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(LoginRequest $request) : Response {
        try {
            $user = User::where('email', $request->email)->first();

            if (! $user || ! Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect.'],
                ]);
            }

            $accessToken = $user->createToken($request->device_name)->plainTextToken;

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

    public function getLoggedUser() : Response {
        $loggedUser = auth('sanctum')->user();
        return ResponseHelper::generate(
            true,
            'success get logged user',
            Response::HTTP_OK,
            [
                'logged_user' => $loggedUser,
            ],
        );
    }

    public function logout() : Response {
        try {
            $loggedUser = auth('sanctum')->user();
            $loggedUser->tokens()->delete();

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

    //* only for super admin
    public function register(RegisterRequest $request) : Response {
        try {
            //* check logged user is super admin
            $loggedUser = auth('sanctum')->user();
            if ($loggedUser->role !== UserRoleEnum::SUPER_ADMIN) {
                throw new UnauthorizedException('action is unauthorized, only for super admin');
            }

            //* register user
            $registeredUser = User::create([
                'id' => Str::orderedUuid(),
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
                'email_verified_at' => now(),
                'role' => $request->role,
            ]);

            //* response data
            return ResponseHelper::generate(
                true,
                'success register user',
                Response::HTTP_OK,
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

    //* only for super admin
    public function getListUsers() : Response {
        try {
            //* check logged user is super admin
            $loggedUser = auth('sanctum')->user();
            if ($loggedUser->role !== UserRoleEnum::SUPER_ADMIN) {
                throw new UnauthorizedException('action is unauthorized, only for super admin');
            }

            //* get list users
            $users = User::select('id', 'name', 'email', 'created_at', 'updated_at')->get();

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
}
;
