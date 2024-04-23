<?php

namespace Tests\Feature\Comments;

use Tests\TestCase;
use App\Models\User;
use App\Models\Article;
use App\Models\Comment;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateCommentsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guest_cannot_create_comments(): void
    {
        $this->postJson(route('api.v1.comments.store'))
            ->assertJsonApiError(
                title: 'Unauthenticated',
                detail: 'This action required authentication.',
                status: '401'
            );

        $this->assertDatabaseCount('comments', 0);
    }

    /** @test */
    public function can_create_comments(): void
    {
        $user = User::factory()->create();
        $article = Article::factory()->create();

        Sanctum::actingAs($user);

        $this->assertDatabaseCount('comments', 0);

        $response = $this->postJson(route('api.v1.comments.store'), [
            'body' => $commentBody = 'Comment body',
            '_relationships' => [
                'article' => $article,
                'author' => $user,
            ],
        ])->assertCreated();

        $comment = Comment::first();

        $response->assertJsonApiResource($comment, [

            'body' => $commentBody,

        ]);

        $this->assertDatabaseCount('comments', 1)
            ->assertDatabaseHas('comments', [
                'body' => $commentBody,
                'article_id' => $article->id,
                'user_id' => $user->id,
            ]);
    }

    /** @test */
    public function body_is_required(): void
    {

        Sanctum::actingAs(User::factory()->create());

        $this->assertDatabaseCount('comments', 0);

        $this->postJson(route('api.v1.comments.store'), [
            'body' => null,

        ])->assertJsonApiValidationErrors('body');

    }

    /** @test */
    public function article_relationship_is_required(): void
    {

        Sanctum::actingAs(User::factory()->create());

        $this->assertDatabaseCount('comments', 0);

        $this->postJson(route('api.v1.comments.store'), [
            'body' => 'Comment Body',

        ])->assertJsonApiValidationErrors('relationships.article');

    }

    /** @test */
    public function article_must_exists_in_database(): void
    {

        Sanctum::actingAs(User::factory()->create());

        $this->assertDatabaseCount('comments', 0);

        $this->postJson(route('api.v1.comments.store'), [
            'body' => 'Comment Body',
            '_relationships' => [
                'article' => Article::factory()->make(),

            ],

        ])->assertJsonApiValidationErrors('relationships.article');

    }

    /** @test */
    public function author_relationship_is_required(): void
    {

        Sanctum::actingAs(User::factory()->create());

        $this->assertDatabaseCount('comments', 0);

        $this->postJson(route('api.v1.comments.store'), [
            'body' => 'Comment Body',
            '_relationships' => [
                'article' => Article::factory()->create(),

            ],

        ])->assertJsonApiValidationErrors('relationships.author');

    }

    /** @test */
    public function author_must_exists_in_database(): void
    {

        Sanctum::actingAs(User::factory()->create());

        $this->assertDatabaseCount('comments', 0);

        $this->postJson(route('api.v1.comments.store'), [
            'body' => 'Comment Body',
            '_relationships' => [
                'article' => Article::factory()->create(),
                'author' => User::factory()->make(['id' => 'uuid']),

            ],

        ])->assertJsonApiValidationErrors('relationships.author');

    }
}
