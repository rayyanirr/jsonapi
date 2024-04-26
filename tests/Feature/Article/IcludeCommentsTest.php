<?php

namespace Tests\Feature\Article;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class IcludeCommentsTest extends TestCase
{
    use RefreshDatabase;


    /** @test */
    public function can_includ_related_comments_of_an_article(): void
    {
        $article = Article::factory()->hasComments(2)->create();

        // articles/the-slug?include=comments

        $url = route('api.v1.articles.show', [

            'article' => $article,
            'include' => 'comments'
        ]);

        $response = $this->getJson($url);

        $response->assertJsonCount(2, 'included');

        $article->comments->map(fn ($comment) => $response->assertJsonFragment([

            'type' => 'comments',
            'id' => (string) $comment->getRouteKey(),
            'attributes' => [
                'body' => $comment->body
            ]

        ]));
    }


}
