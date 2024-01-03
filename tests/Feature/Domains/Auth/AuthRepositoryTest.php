<?php

namespace Tests\Feature\Domains\Auth;

use App\Domains\Auth\AuthRepository;
use App\Domains\Auth\Entities\RegisterUserEntity;
use App\Enums\UserRoleEnum;
use App\Models\User;
use Database\Seeders\TestSeeder\Domains\Auth\AuthRepository\GetUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthRepositoryTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_get_user(): void
    {
        $fakeUser = User::factory()->create();
        $authRepository = new AuthRepository(new User());
        $user = $authRepository->getUser($fakeUser->email);

        $this->assertEquals($fakeUser->id, $user->id);
        $this->assertEquals($fakeUser->email, $user->email);
    }

    public function test_get_list_users() : void {
        $fakeusers = User::factory(10)->create();
        $authRepository = new AuthRepository(new User());
        $users = $authRepository->getListUsers();

        $this->assertDatabaseCount(User::class, $users->count());
    }

    public function test_register_user() : void {
        $validatedRequest = (object)[
            'name' => fake()->name,
            'email' => fake()->email,
            'password' => 'password',
            'role' => UserRoleEnum::ADMIN,
        ];
        $authRepository = new AuthRepository(new User());
        $registerUserEntity = new RegisterUserEntity($validatedRequest);
        $registeredUser = $authRepository->registerUser($registerUserEntity);

        $this->assertEquals($registeredUser->name, $validatedRequest->name);
        $this->assertEquals($registeredUser->email, $validatedRequest->email);
        $this->assertTrue(Hash::check($validatedRequest->password, $registeredUser->password));
        $this->assertEquals($registeredUser->role, $validatedRequest->role);
    }
}
