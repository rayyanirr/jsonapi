<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UpdateArticleTest extends TestCase
{
    use RefreshDatabase;


    /** @test */
    public function can_update_owned_articles()
    {
        $article = Article::factory()->create();

        Sanctum::actingAs($article->author);

        $response = $this->patchJson(route('api.v1.articles.update', $article), [

            'title' => 'Update Articulo',
            'slug' => $article->slug,
            'content' => 'Update Content'
        ]);

        $response->assertOk();

        $response->assertJsonApiResource($article,[
            'title' => 'Update Articulo',
            'slug' => $article->slug,
            'content' => 'Update Content'
        ]);


    }

    /** @test */
    public function title_is_required()
    {
        $article = Article::factory()->create();

        Sanctum::actingAs($article->author);

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
        Sanctum::actingAs($article->author);
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
        Sanctum::actingAs($article->author);
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

        Sanctum::actingAs($article->author);

        $response = $this->patchJson(route('api.v1.articles.update', $article), [
            'slug' => $article2->slug,
            'title' => 'nulll',
            'content' => 'contenido del articulo'

        ]);

        $response->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_not_contain_underscores()
    {
        $article = Article::factory()->create();
        Sanctum::actingAs($article->author);
        $this->patchJson(route('api.v1.articles.update', $article), [
            'slug' => 'prueba_a',
            'title' => 'nulll',
            'content' => 'contenido del articulo'

        ])->assertSee(__('validation.no_underscores', ['attribute' => 'data.attributes.slug']))
            ->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_not_start_with_dashes()
    {
        $article = Article::factory()->create();
        Sanctum::actingAs($article->author);
        $this->patchJson(route('api.v1.articles.update', $article), [
            'slug' => '-pruebaa',
            'title' => 'nulll',
            'content' => 'contenido del articulo'

        ])->assertSee(__('validation.no_starting_dashes', ['attribute' => 'data.attributes.slug']))
            ->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_not_end_with_dashes()
    {
        $article = Article::factory()->create();
        Sanctum::actingAs($article->author);
        $this->patchJson(route('api.v1.articles.update', $article), [
            'slug' => 'pruebaa-',
            'title' => 'nulll',
            'content' => 'contenido del articulo'

        ])->assertSee(__('validation.no_ending_dashes', ['attribute' => 'data.attributes.slug']))
            ->assertJsonApiValidationErrors('slug');
    }


    /** @test */
    public function guests_cannot_update_articles()
    {
        $article = Article::factory()->create();
        $response = $this->patchJson(route('api.v1.articles.update', $article), [
            'title' => 'Update Articulo',
            'slug' => $article->slug,
            'content' => 'Update Content'
        ]);
        $response->assertJsonApiError(
            title: 'Unauthenticated',
            detail : 'This action required authentication.',
            status : '401'
        );

    }


    /** @test */
    public function cannot_update_articles_owned_by_other_users()
    {
        $article = Article::factory()->create();

        Sanctum::actingAs(User::factory()->create());

        $this->patchJson(route('api.v1.articles.update', $article), [

            'title' => 'Update Articulo',
            'slug' => $article->slug,
            'content' => 'Update Content'
        ])->assertForbidden();


    }
}
