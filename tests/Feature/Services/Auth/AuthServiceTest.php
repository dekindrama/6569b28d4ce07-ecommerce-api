<?php

namespace Tests\Feature\Services\Auth;

use App\Domains\Auth\AuthRepositoryInterface;
use App\Enums\UserRoleEnum;
use App\Exceptions\Commons\UnauthorizedException;
use App\Models\User;
use App\Services\Auth\AuthService;
use App\Services\Auth\Entities\LoginEntity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Mockery\MockInterface;
use Tests\TestCase;
use Throwable;

class AuthServiceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_login(): void
    {
        $fakeUser = User::factory()->create();

        //* mock
        $mockAuthRepository = $this->mock(AuthRepositoryInterface::class, function (MockInterface $mock) use ($fakeUser) {
            $mock->shouldReceive('getUser')->once()->andReturn($fakeUser);
        });

        //* action
        $loginEntity = new LoginEntity(
            fake()->email,
            'password',
            'android'
        );
        $service = new AuthService($mockAuthRepository);
        $accessToken = $service->login($loginEntity);

        //* assert
        $this->assertIsString($accessToken);
    }

    public function test_logout_case_authenticated(): void
    {
        $fakeLoggedUser = User::factory()->create();
        Sanctum::actingAs($fakeLoggedUser);

        //* action
        $service = new AuthService($this->app->make(AuthRepositoryInterface::class));
        $service->logout();

        //* assert
        $this->expectNotToPerformAssertions(); //* test to return not, test will fail when error
    }

    public function test_logout_case_unauthenticated(): void
    {
        //* action
        $service = new AuthService($this->app->make(AuthRepositoryInterface::class));

        //* assert
        $this->assertThrows(
            fn () => $service->logout(),
            Throwable::class,
        );
    }

    public function test_get_logged_user(): void
    {
        $fakeLoggedUser = User::factory()->create();
        Sanctum::actingAs($fakeLoggedUser);

        //* action
        $service = new AuthService($this->app->make(AuthRepositoryInterface::class));
        $loggedUser = $service->getLoggedUser();

        //* assert
        $this->assertEquals($fakeLoggedUser, $loggedUser);
    }

    public function test_get_list_users_case_login_as_super_admin(): void
    {
        $fakeLoggedUser = User::factory()->create([
            'role' => UserRoleEnum::SUPER_ADMIN
        ]);
        $fakeUsers = User::factory(10)->create();

        //* mock
        $mockAuthRepository = $this->mock(AuthRepositoryInterface::class, function (MockInterface $mock) use ($fakeUsers) {
            $mock->shouldReceive('getListUsers')->once()->andReturn($fakeUsers);
        });

        //* action
        $service = new AuthService($mockAuthRepository);
        $users = $service->getListUsers($fakeLoggedUser);

        //* assert
        $this->assertEquals($fakeUsers, $users);
    }

    public function test_get_list_users_case_login_as_admin(): void
    {
        $fakeLoggedUser = User::factory()->create([
            'role' => UserRoleEnum::ADMIN
        ]);
        $fakeUsers = User::factory(10)->create();

        //* action
        $service = new AuthService($this->app->make(AuthRepositoryInterface::class));

        //* assert
        $this->assertThrows(
            fn () => $service->getListUsers($fakeLoggedUser),
            UnauthorizedException::class,
        );
    }

    public function test_register_user_case_login_as_super_admin(): void
    {
        $fakeLoggedUser = User::factory()->create([
            'role' => UserRoleEnum::SUPER_ADMIN
        ]);
        $validatedRequest = (object)[
            'name' => fake()->name,
            'email' => fake()->email,
            'password' => 'password',
            'role' => UserRoleEnum::ADMIN,
        ];
        $fakeUser = User::factory()->create([
            'name' => $validatedRequest->name,
            'email' => $validatedRequest->email,
            'password' => Hash::make($validatedRequest->password),
            'role' => $validatedRequest->role,
        ]);

        //* mock
        $mockAuthRepository = $this->mock(AuthRepositoryInterface::class, function (MockInterface $mock) use ($fakeUser) {
            $mock->shouldReceive('registerUser')->once()->andReturn($fakeUser);
        });

        //* action
        $service = new AuthService($mockAuthRepository);
        $registeredUser = $service->register($fakeLoggedUser, $validatedRequest);

        //* assert
        $this->assertEquals($fakeUser, $registeredUser);
    }

    public function test_register_user_case_login_as_admin(): void
    {
        $fakeLoggedUser = User::factory()->create([
            'role' => UserRoleEnum::ADMIN
        ]);
        $validatedRequest = (object)[
            'name' => fake()->name,
            'email' => fake()->email,
            'password' => 'password',
            'role' => UserRoleEnum::ADMIN,
        ];
        $fakeUser = User::factory()->create([
            'name' => $validatedRequest->name,
            'email' => $validatedRequest->email,
            'password' => Hash::make($validatedRequest->password),
            'role' => $validatedRequest->role,
        ]);

        //* action
        $service = new AuthService($this->app->make(AuthRepositoryInterface::class));

        //* assert
        $this->assertThrows(
            fn () => $service->register($fakeLoggedUser, $validatedRequest),
            UnauthorizedException::class,
        );
    }
}
