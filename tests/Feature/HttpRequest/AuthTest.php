<?php

namespace Tests\Feature\HttpRequest;

use App\Enums\UserRoleEnum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_post_login(): void
    {
        $fakeUser = User::factory()->create([
            'role' => UserRoleEnum::SUPER_ADMIN,
            'password' => Hash::make('password'),
        ]);
        Sanctum::actingAs($fakeUser);

        //* action
        $response = $this->post(route('users.login'), [
            'email' => $fakeUser->email,
            'password' => 'password',
            'device_name' => 'android',
        ]);

        //* assert
        $response->assertOk();
        $response->assertJson([
            'status' => true,
        ]);
    }

    public function test_post_logout(): void
    {
        $fakeUser = User::factory()->create([
            'role' => UserRoleEnum::SUPER_ADMIN,
            'password' => Hash::make('password'),
        ]);
        Sanctum::actingAs($fakeUser);

        //* action
        $response = $this->post(route('users.logout'));

        //* assert
        $response->assertOk();
        $response->assertJson([
            'status' => true,
        ]);
    }

    public function test_get_list_users_case_login_as_super_admin(): void
    {
        $fakeUser = User::factory()->create([
            'role' => UserRoleEnum::SUPER_ADMIN,
            'password' => Hash::make('password'),
        ]);
        Sanctum::actingAs($fakeUser);
        $fakeUsers = User::factory(10)->create();

        //* action
        $response = $this->get(route('users.get_list_users'));

        //* assert
        $response->assertOk();
        $response->assertJsonCount(11, 'data.users');
        $response->assertJson([
            'status' => true
        ]);
    }

    public function test_get_list_users_case_login_as_admin(): void
    {
        $fakeUser = User::factory()->create([
            'role' => UserRoleEnum::ADMIN,
            'password' => Hash::make('password'),
        ]);
        Sanctum::actingAs($fakeUser);
        $fakeUsers = User::factory(10)->create();

        //* action
        $response = $this->get(route('users.get_list_users'));

        //* assert
        $response->assertUnauthorized();
        $response->assertJson([
            'status' => false
        ]);
    }

    public function test_post_register_case_login_as_super_admin(): void
    {
        $fakeUser = User::factory()->create([
            'role' => UserRoleEnum::SUPER_ADMIN,
            'password' => Hash::make('password'),
        ]);
        Sanctum::actingAs($fakeUser);
        $validatedRequest = [
            'name' => fake()->name,
            'email' => fake()->email,
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => UserRoleEnum::ADMIN,
        ];

        //* action
        $response = $this->post(route('users.register'), $validatedRequest);

        //* assert
        $response->assertCreated();
        $response->assertJson([
            'status' => true,
            'data' => [
                'user' => [
                    'name' => $validatedRequest['name'],
                    'email' => $validatedRequest['email'],
                    'role' => $validatedRequest['role'],
                ]
            ]
        ]);
    }

    public function test_post_register_case_login_as_admin(): void
    {
        $fakeUser = User::factory()->create([
            'role' => UserRoleEnum::ADMIN,
            'password' => Hash::make('password'),
        ]);
        Sanctum::actingAs($fakeUser);
        $validatedRequest = [
            'name' => fake()->name,
            'email' => fake()->email,
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => UserRoleEnum::ADMIN,
        ];

        //* action
        $response = $this->post(route('users.register'), $validatedRequest);

        //* assert
        $response->assertUnauthorized();
    }

    public function test_get_logged_user(): void
    {
        $fakeUser = User::factory()->create([
            'role' => UserRoleEnum::SUPER_ADMIN,
            'password' => Hash::make('password'),
        ]);
        Sanctum::actingAs($fakeUser);

        //* action
        $response = $this->get(route('users.get_logged_user'));
        // dd($response->getContent());

        //* assert
        $response->assertOk();
        $response->assertJson([
            'status' => true,
            'data' => [
                'logged_user' => [
                    'id' => $fakeUser->id,
                    'name' => $fakeUser->name,
                ],
            ]
        ]);
    }
}
