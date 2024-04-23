<?php

namespace Tests\Feature\Comments;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DeleteCommentsTest extends TestCase
{
    use RefreshDatabase;


    /** @test */
    public function guests_cannot_delete_comments(): void
    {
        $comment = Comment::factory()->create();

        $this->deleteJson(route('api.v1.comments.destroy', $comment))
            ->assertJsonApiError(
            title: 'Unauthenticated',
            detail : 'This action required authentication.',
            status : '401'
        );
    }

    /** @test */
    public function can_delete_owned_comments(): void
    {
        $comment = Comment::factory()->create();

        Sanctum::actingAs($comment->author, ['comment:delete']);

        $this->deleteJson(route('api.v1.comments.destroy', $comment))
            ->assertNoContent();

        $this->assertDatabaseCount('comments', 0);
    }

    /** @test */
    public function cannot_delete_commetns_owned_by_other_users(): void
    {
        $comment = Comment::factory()->create();

        Sanctum::actingAs(User::factory()->create(), ['comment:delete']);

        $this->deleteJson(route('api.v1.comments.destroy', $comment))
            ->assertForbidden();

        $this->assertDatabaseCount('comments', 1);
    }
}
