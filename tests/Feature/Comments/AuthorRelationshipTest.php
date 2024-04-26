<?php

namespace Tests\Feature\Comments;

use Tests\TestCase;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthorRelationshipTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_fetch_the_asociated_author_identifier(): void
    {
        $comment = Comment::factory()->create();
        $url = route('api.v1.comments.relationships.author', $comment);

        $this->getJson($url)
            ->assertExactJson([
                'data' => [
                    'id' => $comment->author->getRouteKey(),
                    'type' => 'authors',
                ],
            ]);
    }

    /** @test */
    public function can_fetch_the_associated_author_resource(): void
    {
        $comment = Comment::factory()->create();

        $url = route('api.v1.comments.author', $comment);

        $response = $this->getJson($url);

        $response->assertJson([
            'data' => [
                'id' => $comment->author->getRouteKey(),
                'type' => 'authors',
                'attributes' => [
                    'name' => $comment->author->name,
                ],
            ],
        ]);
    }

    /** @test */
    public function can_update_the_comments_associated_author(): void
    {
        $author = User::factory()->create();
        $comment = Comment::factory()->create();

        $url = route('api.v1.comments.relationships.author.update', $comment);

        $response = $this->patchJson($url, [
            'data' => [
                'type' => 'authors',
                'id' => $author->getRouteKey(),
            ],
        ]);

        $response->assertExactJson([

            'data' => [
                'type' => 'authors',
                'id' => $author->getRouteKey(),
            ],
        ]);

        $this->assertDatabaseHas('comments', [

            'body' => $comment->body,
            'user_id' => $author->id,
        ]);

    }

    /** @test */
    public function autthor_must_exist_in_database(): void
    {
        $comment = Comment::factory()->create();

        $url = route('api.v1.comments.relationships.author.update', $comment);

        $this->patchJson($url, [
            'data' => [
                'type' => 'authors',
                'id' => 'non-existing',
            ],
        ])->assertJsonApiValidationErrors('data.id');

        $this->assertDatabaseHas('comments', [

            'body' => $comment->body,
            'user_id' => $comment->user_id,
        ]);

    }
}
