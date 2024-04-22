<?php

use App\Models\User;
use App\Http\Controllers\User\UserController;
use App\Interfaces\UserRepositoryInterface;
use Illuminate\Http\Request;
// use PHPUnit\Framework\TestCase;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\Passport;
use Tests\TestCase;

use Illuminate\Foundation\Testing\RefreshDatabase;

class FetchSingleUserTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

    public function testShowValidUser()
    {
        // Arrange
        $userData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'ValidPassword1$',
        ];

        // Act
        $user1 = $this->postJson('api/v1/users/store', $userData);
        $user = json_decode($user1->getContent(), true);

        $response = $this->getJson('api/v1/users/' .  $user['data']['id']);

        $responseData = json_decode($response->getContent(), true);

        $this->assertEquals(true, $responseData['success']);
        $this->assertEquals($user['data']['id'], $responseData['data']['id']);
        $this->assertEquals($user['data']['name'], $responseData['data']['name']);
        $this->assertEquals($user['data']['email'], $responseData['data']['email']);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testShowInvalidUserId()
    {
        // Assuming an invalid user id, which does not exist in the database
        $invalidUserId = 9999;

        $request = Request::create('/users/' . $invalidUserId, 'GET');

        $response = $this->getJson('api/v1/users/' .  $invalidUserId);

        $responseData = json_decode($response->getContent(), true);

        $this->assertEquals(false, $responseData['success']);
        $this->assertEquals("invalid id", $responseData['data']);
        $this->assertEquals(200, $response->getStatusCode());
    }

    // public function testDeleteUser()
    // {
    //     $userData = [
    //         'name' => $this->faker->name,
    //         'email' => $this->faker->unique()->safeEmail,
    //         'password' => 'ValidPassword1$',
    //     ];

    //     // Act
    //     $userNew = $this->postJson('api/v1/users/store', $userData);
    //     $user = json_decode($userNew->getContent(), true);
    //     $user = User::factory()->make();

    //     Passport::actingAs($user);
    //     $token = $user->generateToken();

    //     $headers = ['Authorization' => 'Bearer' . " " . $token];
    //     $deleted = $this->deleteJson('api/v1/users/delete/' .  $user->email, $headers)
    //     ->assertOk();

    //     $responseDeletedData = json_decode($deleted->getContent(), true);

    //     $this->assertEquals(true, $responseDeletedData["success"]);


    //     $this->assertDatabaseMissing('users', ['email' => $user->email]);
    // }
}
