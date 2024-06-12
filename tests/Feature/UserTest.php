<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Database\Seeders\UserSeeder;
use App\Models\User;

use function PHPUnit\Framework\assertNotEquals;

class UserTest extends TestCase
{
    public function testRegisterSuccess(): void
    {
        $this->post('/api/users', [
            'username' => 'jhondoe',
            'password' => 'password'
        ])->assertStatus(201)
            ->assertJson([
                "data" => [
                    'username' => 'jhondoe',
                    ]
            ]);
    }
    public function testRegisterFailed(): void
    {
        $this->post('/api/users', [
            'username' => '',
            'password' => ''
        ])->assertStatus(422)
            ->assertJson([
                "errors" => [
                    'username' => [
                        'The username field is required.'
                    ],
                    'password' => [
                        'The password field is required.'
                    ]
                ]
            ]);
    }
    public function testRegisterUsernameExists(): void
    {
        $this->testRegisterSuccess();
        $this->post('/api/users', [
            'username' => 'jhondoe',
            'password' => 'password'
        ])->assertStatus(422)
            ->assertJson([
                "errors" => [
                    'username' => [
                        'Username already exists'
                    ],
                ]
            ]);
    }

    public function testLoginSuccess(): void
    {
        $this->seed([UserSeeder::class]);
        $this->post('/api/users/login', [
            'username' => 'janedoe',
            'password' => 'password'
        ])->assertStatus(200)
            ->assertJsonStructure([
                "data" => [
                    'username',
                    'token'
                ]
            ]);
        $user = User::where('username', 'janedoe')->first();
        self::assertNotNull($user->token);
    }
    public function testLoginFailedUsernameNotFound(): void
    {
        $this->seed([UserSeeder::class]);
        $this->post('/api/users/login', [
            'username' => 'janedoe123',
            'password' => 'password'
        ])->assertStatus(401)
            ->assertJson([
                "errors" => [
                    'message' => [
                        'Invalid username or password'
                    ]
                ]
            ]);
    }
    public function testLoginFailedPasswordWrong(): void
    {
        $this->seed([UserSeeder::class]);
        $this->post('/api/users/login', [
            'username' => 'janedoe',
            'password' => 'wrongpassword'
        ])->assertStatus(401)
            ->assertJson([
                "errors" => [
                    'message' => [
                        'Invalid username or password'
                    ]
                ]
            ]);
    }

    public function testGetUserSuccess(): void
    {
        $this->seed([UserSeeder::class]);
        $this->get('/api/users/current',[
            'Authorization' => 'janedoe_token'
        ])
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'janedoe',
                ]
            ]);
    }
    public function testGetUserUnauthorized(): void
    {
        $this->seed([UserSeeder::class]);
        $this->get('/api/users/current')
            ->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Unauthorized'
                    ]
                ]
            ]);
    }
    public function testGetUserInvalidToken(): void
    {
        $this->seed([UserSeeder::class]);
        $this->get('/api/users/current',[
            'Authorization' => 'invalid_token'
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Unauthorized'
                    ]
                ]
            ]);
    }

    public function testUpdateUserSuccess(): void
    {
        $this->seed([UserSeeder::class]);
        $oldUser = User::where('username', 'janedoe')->first();
        $this->patch('/api/users/current', [
            'password' => 'newpassword'
        ],[
            'Authorization' => 'janedoe_token'
            ])->assertStatus(200)
            ->assertJson([
                'message' => 'password updated'
                ]);

        $newUser = User::where('username', 'janedoe')->first();
        self::assertNotEquals($oldUser->password, $newUser->password);
    }
    public function testUpdateUserUnauthorized(): void
    {
        $this->seed([UserSeeder::class]);
        $this->patch('/api/users/current', [
            'password' => 'newpassword'
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Unauthorized'
                    ]
                ]
            ]);
    }
    public function testUpdateUserFailedValidation(): void
    {
        $this->seed([UserSeeder::class]);
        $this->patch('/api/users/current', [
            'password' => 'abc'
        ],[
            'Authorization' => 'janedoe_token'
        ])->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'password' => [
                        'The password field must be at least 8 characters.'
                    ]
                ]
            ]);
    }

    public function testUpdateUsernull(): void
    {
        $this->seed([UserSeeder::class]);
        $this->patch('/api/users/current', [],[
            'Authorization' => 'janedoe_token'
        ])->assertStatus(200)
            ->assertJson([
                'message' => 'nothing change'
            ]);
    }

    public function testLogoutSuccess(): void
    {
        $this->seed([UserSeeder::class]);
        $this->delete(uri: '/api/users/logout', headers: [
            'Authorization' => 'janedoe_token'
        ])->assertStatus(200)
            ->assertJson([
                'data' => 'true'
            ]);
        $user = User::where('username', 'janedoe')->first();
        self::assertNull($user->token);
    }
    public function testLogoutUnauthorized(): void
    {
        $this->seed([UserSeeder::class]);
        $this->delete(uri: '/api/users/logout', headers: [
            'Authorization' => 'invalid_token'
        ])
            ->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Unauthorized'
                    ]
                ]
            ]);
    }
}
