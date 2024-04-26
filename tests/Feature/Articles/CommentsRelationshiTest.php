<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CommentsRelationshiTest extends TestCase
{
    use RefreshDatabase;


    /** @test */
    public function can_fectch_the_associated_comments_identifiers(): void
    {
        $article = Article::factory()->hasComments(2)->create();

        $url = route('api.v1.articles.relationships.comments',$article);

        $response = $this->getJson($url);

        $response->assertJsonCount(2,'data');

        $article->comments->map(fn ($comment) =>

        $response->assertJsonFragment([
            'id' => (string) $comment->getRouteKey(),
            'type' => 'comments'
        ]));
    }
}
