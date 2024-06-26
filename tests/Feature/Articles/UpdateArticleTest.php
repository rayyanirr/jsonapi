<?php

namespace Tests\Feature\Articles;

use Tests\TestCase;
use App\Models\User;
use App\Models\Article;
use App\Models\Category;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateArticleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_update_owned_articles()
    {
        $article = Article::factory()->create();

        Sanctum::actingAs($article->author, ['article:update']);

        $response = $this->patchJson(route('api.v1.articles.update', $article), [

            'title' => 'Update Articulo',
            'slug' => $article->slug,
            'content' => 'Update Content',
        ]);

        $response->assertOk();

        $response->assertJsonApiResource($article, [
            'title' => 'Update Articulo',
            'slug' => $article->slug,
            'content' => 'Update Content',
        ]);

    }

    /** @test */
    public function title_is_required()
    {
        $article = Article::factory()->create();

        Sanctum::actingAs($article->author);

        $response = $this->patchJson(route('api.v1.articles.update', $article), [
            'slug' => 'nuevo-articulo',
            'content' => 'contenido del articulo',

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
            'content' => 'contenido del articulo',

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
            'content' => 'contenido del articulo',

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
            'content' => 'contenido del articulo',

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
            'content' => 'contenido del articulo',

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
            'content' => 'contenido del articulo',

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
            'content' => 'Update Content',
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

        Sanctum::actingAs(User::factory()->create(), ['article:update']);

        $this->patchJson(route('api.v1.articles.update', $article), [

            'title' => 'Update Articulo',
            'slug' => $article->slug,
            'content' => 'Update Content',
        ])->assertForbidden();

    }

    /** @test */
    public function can_update_owned_articles_with_relationships()
    {
        $article = Article::factory()->create();
        $category = Category::factory()->create();

        Sanctum::actingAs($article->author, ['article:update']);

        $response = $this->patchJson(route('api.v1.articles.update', $article), [

            'title' => 'Update Articulo',
            'slug' => $article->slug,
            'content' => 'Update Content',
            '_relationships' => [
                'category' => $category,
            ],
        ]);

        $response->assertOk();

        $response->assertJsonApiResource($article, [
            'title' => 'Update Articulo',
            'slug' => $article->slug,
            'content' => 'Update Content',

        ]);

        $this->assertDatabaseHas('articles', [
            'title' => 'Update Articulo',
            'category_id' => $category->id,

        ]);

    }
}
