<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use App\Models\Permission;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_issue_access_tokens(): void
    {
        $this->withoutJsonApiDocumentFormatting();

        $user = User::factory()->create();

        $data = $this->validCredentials([
            'email' => $user->email,
        ]);

        $response = $this->postJson(route('api.v1.login'), $data);

        $token = $response->json('plain-text-token');

        $dbToken = PersonalAccessToken::findToken($token);

        $this->assertTrue($dbToken->tokenable->is($user));

    }

    /** @test */
    public function password_must_be_valid(): void
    {
        $this->withoutJsonApiDocumentFormatting();

        $this->withoutJsonApiHeaders();

        $user = User::factory()->create();

        $data = $this->validCredentials([
            'email' => $user->email,
            'password' => 'incorrect',
        ]);

        $response = $this->postJson(route('api.v1.login'), $data);

        $response->assertJsonValidationErrorFor('email');
    }

    /** @test */
    public function user_must_be_registered(): void
    {
        $this->withoutJsonApiDocumentFormatting();

        $this->withoutJsonApiHeaders();

        $data = $this->validCredentials();

        $response = $this->postJson(route('api.v1.login'), $data);

        $response->assertJsonValidationErrorFor('email');
    }

    /** @test */
    public function email_is_required(): void
    {
        $this->withoutJsonApiDocumentFormatting();
        $this->withoutJsonApiHeaders();

        $data = $this->validCredentials(['email' => null]);

        $response = $this->postJson(route('api.v1.login'), $data);

        $response->assertJsonValidationErrors(['email' => 'required']);
    }

    /** @test */
    public function email_must_be_valid(): void
    {
        $this->withoutJsonApiDocumentFormatting();
        $this->withoutJsonApiHeaders();

        $data = $this->validCredentials(['email' => 'invalid']);

        $response = $this->postJson(route('api.v1.login'), $data);

        $response->assertJsonValidationErrors(['email' => 'email']);
    }

    /** @test */
    public function password_is_required(): void
    {
        $this->withoutJsonApiDocumentFormatting();

        $this->withoutJsonApiHeaders();

        $data = $this->validCredentials(['password' => null]);

        $response = $this->postJson(route('api.v1.login'), $data);

        $response->assertJsonValidationErrors(['password' => 'required']);
    }

    /** @test */
    public function device_name_is_required(): void
    {
        $this->withoutJsonApiDocumentFormatting();
        $this->withoutJsonApiHeaders();

        $data = $this->validCredentials(['device_name' => null]);

        $response = $this->postJson(route('api.v1.login'), $data);

        $response->assertJsonValidationErrors(['device_name' => 'required']);
    }

    /** @test */
    public function user_perrmissions_are_assigned_as_abilities_to_the_token(): void
    {
        $this->withoutJsonApiDocumentFormatting();

        $user = User::factory()->create();

        $permission1 = Permission::factory()->create();
        $permission2 = Permission::factory()->create();
        $permission3 = Permission::factory()->create();

        $user->givePermissionTo($permission1);
        $user->givePermissionTo($permission2);

        $data = $this->validCredentials([
            'email' => $user->email,
        ]);

        $response = $this->postJson(route('api.v1.login'), $data);

        $token = $response->json('plain-text-token');

        $dbToken = PersonalAccessToken::findToken($token);

        $this->assertTrue($dbToken->can($permission1->name));
        $this->assertTrue($dbToken->can($permission2->name));
        $this->assertFalse($dbToken->can($permission3->name));

    }

    /** @test */
    public function only_one_access_token_can_be_issued_at_a_time(): void
    {
        $user = User::factory()->create();

        $accessToken = $user->createToken($user->name)->plainTextToken;

        $this->withHeader('Authorization', "Bearer $accessToken")
            ->postJson(route('api.v1.login'))
            ->assertNoContent();

        $this->assertCount(1, $user->tokens);
    }

    protected function validCredentials(mixed $overrides = []): array
    {
        return array_merge([
            'email' => 'rayyanir@example.com',
            'password' => 'password',
            'device_name' => 'My device',
        ], $overrides);

    }
}
