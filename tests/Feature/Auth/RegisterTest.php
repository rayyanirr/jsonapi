<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutJsonApiDocumentFormatting();

    }


    /** @test */
    public function can_register(): void
    {
        $response = $this->postJson(route('api.v1.register'), $data = $this->validCredentials());

        $this->assertDatabaseHas('users',[
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        $token = $response->json('plain-text-token');

        $this->assertNotNull(PersonalAccessToken::findToken($token), 'The token plain is invalid');


    }


    /** @test */
    public function authenticated_users_cannot_register_again(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson(route('api.v1.register'))
            ->assertNoContent(204);

    }

    /** @test */
    public function name_in_register_is_required(): void
    {
        $this->withoutJsonApiHeaders();

        $data = $this->validCredentials(['name' => '']);

        $response = $this->postJson(route('api.v1.register'), $data);

         $response->assertJsonValidationErrorFor('name');

    }

    /** @test */
    public function email_in_register_is_required(): void
    {
        $this->withoutJsonApiHeaders();

        $data = $this->validCredentials(['email' => '']);

        $response = $this->postJson(route('api.v1.register'), $data);

         $response->assertJsonValidationErrorFor('email');

    }

    /** @test */
    public function email_in_register_must_be_valid(): void
    {
        $this->withoutJsonApiHeaders();

        $data = $this->validCredentials(['email' => 'invalid']);

        $response = $this->postJson(route('api.v1.register'), $data);

       $response->assertJsonValidationErrors(['email' => 'email']);
    }

    /** @test */
    public function email_in_register_must_be_unique(): void
    {
        $this->withoutJsonApiHeaders();

        $user = User::factory()->create();

        $data = $this->validCredentials(['email' => $user->email]);

        $response = $this->postJson(route('api.v1.register'), $data);

       $response->assertJsonValidationErrors('email');
    }


    /** @test */
    public function password_in_register_is_required(): void
    {
        $this->withoutJsonApiHeaders();

        $data = $this->validCredentials(['password' => '']);

        $response = $this->postJson(route('api.v1.register'), $data);

         $response->assertJsonValidationErrorFor('password');

    }

    /** @test */
    public function password_in_register_must_be_confirmed(): void
    {
        $this->withoutJsonApiHeaders();

        $data = $this->validCredentials(['password' => 'password', 'password_confirmation' => 'not-confirmed']);

        $response = $this->postJson(route('api.v1.register'), $data);

         $response->assertJsonValidationErrorFor('password');

    }

     /** @test */
     public function device_name_register_is_required(): void
     {
         $this->withoutJsonApiDocumentFormatting();

         $this->withoutJsonApiHeaders();

         $data = $this->validCredentials(['device_name' => '']);

         $response = $this->postJson(route('api.v1.register'), $data);

        $response->assertJsonValidationErrorFor('device_name');
     }

    protected function validCredentials(mixed $overrides = []): array
    {
        return  array_merge([
                'name' => 'Rayyanir Rosales',
                'email' => 'rayyanir@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
                'device_name' => 'My device',
        ], $overrides);

    }
}
