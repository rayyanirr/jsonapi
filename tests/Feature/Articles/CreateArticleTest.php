<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateArticleTest extends TestCase
{
    use RefreshDatabase;


    /** @test */
    public function can_create_articles()
    {
        $response = $this->postJson(route('api.v1.articles.create'), [
            'data' => [
                'type' => 'articles',
                'attributes' => [
                    'title' => 'Nuevo Articulo',
                    'slug' => 'nuevo-articulo',
                    'content' => 'contenido del articulo'
                ],
            ]
        ]);

        $response->assertCreated();

        $article = Article::first();

        $response->assertHeader(
            'Location',
            route('api.v1.articles.show', $article)
        );

        $response->assertExactJson([
            'data' => [
                'type' => 'articles',
                'id' => (string) $article->getRouteKey(),
                'attributes' => [
                    'title' => 'Nuevo Articulo',
                    'slug' => 'nuevo-articulo',
                    'content' => 'contenido del articulo'
                ],
                'links' => [
                    'self' => route('api.v1.articles.show', $article),

                ]
            ]

        ]);
    }
    /** @test */
    public function title_is_required()
    {
        $response = $this->postJson(route('api.v1.articles.create'), [
            'data' => [
                'type' => 'articles',
                'attributes' => [

                    'slug' => 'nuevo-articulo',
                    'content' => 'contenido del articulo'
                ],
            ]
        ]);

        //$response->assertJsonValidationErrors('data.attributes.title');

        $response->assertJsonStructure([
            'errors' => [
                ['title', 'detail', 'source' => ['pointer']]
            ]
        ])->assertJsonFragment([
            'source' => ['pointer'=> '/data/attributes/title']
        ]);
    }

    /** @test */
    public function slug_is_required()
    {
        $response = $this->postJson(route('api.v1.articles.create'), [
            'data' => [
                'type' => 'articles',
                'attributes' => [
                    'title' => 'nu',

                    'content' => 'contenido del articulo'
                ],
            ]
        ]);

        //$response->assertJsonValidationErrors('data.attributes.slug');
        $response->assertJsonStructure([
            'errors' => [
                ['title', 'detail', 'source' => ['pointer']]
            ]
        ])->assertJsonFragment([
            'source' => ['pointer'=> '/data/attributes/slug']
        ])
        ->assertHeader('content-type', 'application/vnd.api+json')
        ->assertStatus(422);
    }

    /** @test */
    public function content_is_required()
    {
        $response = $this->postJson(route('api.v1.articles.create'), [
            'data' => [
                'type' => 'articles',
                'attributes' => [
                    'title' => 'nuevo articulo',
                    'slug' => 'nuevo-articulo',

                ],
            ]
        ]);

        $response->assertJsonValidationErrors('data.attributes.content');
    }
}
