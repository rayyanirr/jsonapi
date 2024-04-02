<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateArticleTest extends TestCase
{
    use RefreshDatabase;


    /** @test */
    public function can_update_articles()
    {
        $article = Article::factory()->create();

        $response = $this->patchJson(route('api.v1.articles.update', $article), [

            'title' => 'Update Articulo',
            'slug' => $article->slug,
            'content' => 'Update Content'
        ]);

        $response->assertOk();

        $response->assertHeader(
            'Location',
            route('api.v1.articles.show', $article)
        );

        $response->assertExactJson([
            'data' => [
                'type' => 'articles',
                'id' => (string) $article->getRouteKey(),
                'attributes' => [
                    'title' => 'Update Articulo',
                    'slug' => $article->slug,
                    'content' => 'Update Content'
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
        $article = Article::factory()->create();
        $response = $this->patchJson(route('api.v1.articles.update', $article), [
            'slug' => 'nuevo-articulo',
            'content' => 'contenido del articulo'

        ]);

        $response->assertJsonApiValidationErrors('title');
    }

    /** @test */
    public function slug_is_required()
    {
        $article = Article::factory()->create();
        $response = $this->patchJson(route('api.v1.articles.update', $article), [

            'title' => 'nulll',
            'content' => 'contenido del articulo'

        ]);

        $response->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function content_is_required()
    {
        $article = Article::factory()->create();
        $response = $this->patchJson(route('api.v1.articles.update', $article), [

            'title' => 'nuevo articulo',
            'slug' => 'nuevo-articulo',


        ]);

        $response->assertJsonApiValidationErrors('content');
    }

    /** @test */
    public function slug_must_be_unique_update()
    {
        $article = Article::factory()->create();
        $article2 = Article::factory()->create();

        $response = $this->patchJson(route('api.v1.articles.update', $article), [
            'slug' => $article2->slug,
            'title' => 'nulll',
            'content' => 'contenido del articulo'

        ]);

        $response->assertJsonApiValidationErrors('slug');
    }
}
