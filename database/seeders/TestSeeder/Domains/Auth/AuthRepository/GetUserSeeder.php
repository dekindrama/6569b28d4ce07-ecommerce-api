<?php

namespace Database\Seeders\TestSeeder\Domains\Auth\AuthRepository;

use App\Enums\UserRoleEnum;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GetUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'role' => UserRoleEnum::SUPER_ADMIN,
        ]);

        User::factory(10)->create();
    }
}
