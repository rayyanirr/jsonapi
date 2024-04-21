<?php

namespace Tests\Feature\Articles;

use Tests\TestCase;
use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;

class IncludeAuthorTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_include_related_author_of_an_article(): void
    {
        $article = Article::factory()->create();

        $url = route('api.v1.articles.show', [
            'article' => $article,
            'include' => 'author',

        ]);

        $this->getJson($url)->assertJson([

            'included' => [
                [
                    'type' => 'authors',
                    'id' => $article->author->getRouteKey(),
                    'attributes' => [
                        'name' => $article->author->name,
                    ],

                ],
            ],
        ]);
    }

    /** @test */
    public function can_include_related_authors_of_multiple_article(): void
    {
        $article = Article::factory()->create()->load('author');
        $article1 = Article::factory()->create()->load('author');

        $url = route('api.v1.articles.index', [

            'include' => 'author',

        ]);

        $this->getJson($url)->assertJson([

            'included' => [
                [
                    'type' => 'authors',
                    'id' => $article->author->getRouteKey(),
                    'attributes' => [
                        'name' => $article->author->name,
                    ],

                ],
                [
                    'type' => 'authors',
                    'id' => $article1->author->getRouteKey(),
                    'attributes' => [
                        'name' => $article1->author->name,
                    ],

                ],
            ],
        ]);
    }
}
