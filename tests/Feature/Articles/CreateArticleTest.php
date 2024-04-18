<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CreateArticleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_create_articles()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson(route('api.v1.articles.store'), [

            'title' => 'Nuevo Articulo',
            'slug' => 'nuevo-articulo',
            'content' => 'contenido del articulo',
            '_relationships' => [
                'category' => $category,
                'author' => $user
            ]
        ]);

        $response->assertCreated();

        $article = Article::first();

        $this->assertDatabaseHas('articles', [
            'title' => 'Nuevo Articulo',
            'user_id' => $user->id,
            'category_id' => $category->id
        ]);

        $response->assertHeader(
            'Location',
            route('api.v1.articles.show', $article)
        );

        $response->assertJson([
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
        Sanctum::actingAs(User::factory()->create());
        $response = $this->postJson(route('api.v1.articles.store'), [

            'slug' => 'nuevo-articulo',
            'content' => 'contenido del articulo'

        ]);

        $response->assertJsonApiValidationErrors('title');
    }

    /** @test */
    public function slug_is_required()
    {
        Sanctum::actingAs(User::factory()->create());
        $response = $this->postJson(route('api.v1.articles.store'), [

            'title' => 'nulll',
            'content' => 'contenido del articulo'

        ]);

        $response->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_be_unique()
    {
        Sanctum::actingAs(User::factory()->create());
        $article = Article::factory()->create();

        $response = $this->postJson(route('api.v1.articles.store'), [
            'slug' => $article->slug,
            'title' => 'nulll',
            'content' => 'contenido del articulo'

        ]);

        $response->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_only_contain_letters_numbers_and_dashes()
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson(route('api.v1.articles.store'), [
            'slug' => '$%~&',
            'title' => 'nulll',
            'content' => 'contenido del articulo'

        ]);

        $response->assertJsonApiValidationErrors('slug');
    }

     /** @test */
     public function slug_must_not_contain_underscores()
     {
        Sanctum::actingAs(User::factory()->create());
          $this->postJson(route('api.v1.articles.store'), [
             'slug' => 'prueba_a',
             'title' => 'nulll',
             'content' => 'contenido del articulo'

         ])->assertSee(__('validation.no_underscores',['attribute' => 'data.attributes.slug']))
           ->assertJsonApiValidationErrors('slug');
     }

     /** @test */
     public function slug_must_not_start_with_dashes()
     {
        Sanctum::actingAs(User::factory()->create());
         $response = $this->postJson(route('api.v1.articles.store'), [
             'slug' => '-pruebaa',
             'title' => 'nulll',
             'content' => 'contenido del articulo'

         ])->assertSee(__('validation.no_starting_dashes',['attribute' => 'data.attributes.slug']))
         ->assertJsonApiValidationErrors('slug');
     }

     /** @test */
     public function slug_must_not_end_with_dashes()
     {
        Sanctum::actingAs(User::factory()->create());
         $response = $this->postJson(route('api.v1.articles.store'), [
             'slug' => 'pruebaa-',
             'title' => 'nulll',
             'content' => 'contenido del articulo'

         ])->assertSee(__('validation.no_ending_dashes',['attribute' => 'data.attributes.slug']))
         ->assertJsonApiValidationErrors('slug');
     }

    /** @test */
    public function content_is_required()
    {
        Sanctum::actingAs(User::factory()->create());
        $response = $this->postJson(route('api.v1.articles.store'), [

            'title' => 'nuevo articulo',
            'slug' => 'nuevo-articulo',


        ]);

        $response->assertJsonApiValidationErrors('content');
    }


    /** @test */
    public function category_relations_is_required()
    {
        Sanctum::actingAs(User::factory()->create());
        $response = $this->postJson(route('api.v1.articles.store'), [

            'title' => 'nuevo articulo',
            'slug' => 'nuevo-articulo',
            'content' => 'contenido del articulo',

        ]);

        $response->assertJsonApiValidationErrors('data.relationships.category.data.id');
    }


    /** @test */
    public function category_must_exists_in_database()
    {
        Sanctum::actingAs(User::factory()->create());
        $response = $this->postJson(route('api.v1.articles.store'), [

            'title' => 'nuevo articulo',
            'slug' => 'nuevo-articulo',
            'content' => 'contenido del articulo',
            '_relationships' => [
                'category' => Category::factory()->make()
            ]
        ]);

        $response->assertJsonApiValidationErrors('relationships.category');
    }

    /** @test */
    public function guests_cannot_create_articles()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $response = $this->postJson(route('api.v1.articles.store'), [

            'title' => 'Nuevo Articulo',
            'slug' => 'nuevo-articulo',
            'content' => 'contenido del articulo',
            '_relationships' => [
                'category' => $category,
                'author' => $user
            ]
        ]);

        $response->assertUnauthorized();

        //$response->assertJsonApiError();

        $this->assertDatabaseCount('articles',0);

    }
}
