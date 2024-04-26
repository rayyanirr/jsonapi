<?php

namespace Tests\Feature\Articles;

use Tests\TestCase;
use App\Models\Article;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CommentsRelationshiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_fectch_the_associated_comments_identifiers(): void
    {
        $article = Article::factory()->hasComments(2)->create();

        $url = route('api.v1.articles.relationships.comments', $article);

        $response = $this->getJson($url);

        $response->assertJsonCount(2, 'data');

        $article->comments->map(fn ($comment) => $response->assertJsonFragment([
            'id' => (string) $comment->getRouteKey(),
            'type' => 'comments',
        ]));
    }

    /** @test */
    public function it_returns_an_empty_array_when_there_are_no_associated_comments(): void
    {
        $article = Article::factory()->create();

        $url = route('api.v1.articles.relationships.comments', $article);

        $response = $this->getJson($url);

        $response->assertJsonCount(0, 'data');

        $response->assertExactJson([
            'data' => []
        ]);
    }

    /** @test */
    public function can_fetch_the_asociated_comments_resource(): void
    {
        $article = Article::factory()->hasComments(2)->create();

        $url = route('api.v1.articles.comments', $article);

        $response = $this->getJson($url);

        $response->assertJson([
            'data' => [
                [
                    'id' => (string)$article->comments[0]->getRouteKey(),
                    'type' => 'comments',
                    'attributes' => [
                        'body' => $article->comments[0]->body
                    ]
                ],
                [
                    'id' => (string)$article->comments[1]->getRouteKey(),
                    'type' => 'comments',
                    'attributes' => [
                        'body' => $article->comments[1]->body
                    ]
                ]
            ]
        ]);
    }

     /** @test */
    public function can_update_the_asociated_comments(): void
    {
        $comments = Comment::factory(2)->create();

        $article = Article::factory()->create();

        $url = route('api.v1.articles.relationships.comments.update', $article);

        $response = $this->patchJson($url, [
            'data' => [
                [
                    'type' => 'comments',
                    'id' => $comments[0]->getRouteKey()
                ],
                [
                    'type' => 'comments',
                    'id' => $comments[1]->getRouteKey()
                ],
            ]
        ]);

        $response->assertJsonCount(2, 'data');

        $comments->map(fn ($comment) => $response->assertJsonFragment([
            'type' => 'comments',
            'id' => (string)$comment->getRouteKey(),
        ]));

        $comments->map(fn ($comment) => $this->assertDatabaseHas('comments', [

            'body' => $comment->body,
            'article_id' => $article->id
        ]));
    }
}
