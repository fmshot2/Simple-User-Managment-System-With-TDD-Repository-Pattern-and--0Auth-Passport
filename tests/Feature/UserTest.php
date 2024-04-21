<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

    /** @test */
    public function it_stores_a_user()
    {
        // Arrange
        $userData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'ValidPassword1$',
        ];

        // Act
        $response = $this->postJson('api/v1/users/store', $userData);

        // Assert
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
            ],
        ]);

        $this->assertDatabaseHas('users', [
            'name' => $userData['name'],
            'email' => $userData['email'],
        ]);

        // Ensure password is hashed
        $user = User::where('email', $userData['email'])->first();
        $this->assertTrue(Hash::check($userData['password'], $user->password));

        // Rollback transaction
        DB::rollBack();
    }

    /** @test */
    public function it_validates_user_creation_with_invalid_password()
    {
        // Arrange: Invalid password (less than 8 characters)
        $invalidPasswordData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'Weak1$', // Password less than 8 characters
        ];

        // Act
        $response = $this->postJson('api/v1/users/store', $invalidPasswordData);

        // Assert
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        // Rollback transaction
        DB::rollBack();
    }
}
