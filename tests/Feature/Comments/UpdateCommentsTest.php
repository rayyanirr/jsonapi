<?php

namespace Tests\Feature\Comments;

use Tests\TestCase;
use App\Models\User;
use App\Models\Article;
use App\Models\Comment;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateCommentsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */

    /** @test */
    public function guests_cannot_update_comments()
    {
        $comment = Comment::factory()->create();
        $response = $this->patchJson(route('api.v1.comments.update', $comment));
        $response->assertJsonApiError(
            title: 'Unauthenticated',
            detail : 'This action required authentication.',
            status : '401'
        );

    }

    /** @test */
    public function can_update_owned_comments()
    {
        $comment = Comment::factory()->create();

        Sanctum::actingAs($comment->author, ['comment:update']);

        $response = $this->patchJson(route('api.v1.comments.update', $comment), [
            'body' => 'Update Content',
        ]);

        $response->assertJsonApiResource($comment, [
            'body' => 'Update Content',
        ])->assertOk();

    }

    /** @test */
    public function body_is_required()
    {
        $comment = Comment::factory()->create();

        Sanctum::actingAs($comment->author);

        $this->patchJson(route('api.v1.comments.update', $comment), [
            'body' => null,
        ])->assertJsonApiValidationErrors('body');
    }

    /** @test */
    public function can_update_owned_comments_with_relationships()
    {
        $comment = Comment::factory()->create();
        $article = Article::factory()->create();

        Sanctum::actingAs($comment->author, ['comment:update']);

        $response = $this->patchJson(route('api.v1.comments.update', $comment), [
            'body' => 'Update Content',

        ])->assertOk();

        $response->assertJsonApiResource($comment, [
            'body' => 'Update Content',

        ]);

        //$this->assertTrue($article->is($comment->fresh()->article));

        $this->assertDatabaseHas('comments', [
            'body' => 'Update Content',
            'article_id' => $comment->article->id,
            'user_id' => $comment->author->id,
        ]);
    }

    /** @test */
    public function cannot_update_comments_owned_by_other_users()
    {
        $comment = Comment::factory()->create();
        $article = Article::factory()->create();

        Sanctum::actingAs(User::factory()->create(), ['comment:update']);

        $this->patchJson(route('api.v1.comments.update', $comment), [
            'body' => 'Update Content',
            '_relationships' => [
                'article' => $article,
                'author' => $comment->author,
            ],
        ])->assertForbidden();

    }
}
