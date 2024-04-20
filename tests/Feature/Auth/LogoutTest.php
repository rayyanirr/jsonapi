<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;


    /** @test */
    public function can_logout(): void
    {
        $user = User::factory()->create();

        $accessToken = $user->createToken($user->name)->plainTextToken;

        $this->withHeader('Authorization', "Bearer $accessToken")
                ->postJson(route('api.v1.logout'))
                ->assertNoContent();

        $this->assertNull(PersonalAccessToken::findToken($accessToken));
    }
}
