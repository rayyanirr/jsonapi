<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DeleteArticleTest extends TestCase
{
    use RefreshDatabase;


    /** @test */
    public function can_delete_owned_articles(): void
    {
        $article = Article::factory()->create();

        Sanctum::actingAs($article->author);

        $this->deleteJson(route('api.v1.articles.destroy', $article))
        ->assertNoContent();

        $this->assertDatabaseCount('articles', 0);
    }

     /** @test */
     public function guests_cannot_delete_articles(): void
     {
         $article = Article::factory()->create();

         $response =  $this->deleteJson(route('api.v1.articles.destroy', $article));

         $response->assertJsonApiError(
            title: 'Unauthenticated',
            detail : 'This action required authentication.',
            status : '401'
        );
     }

     /** @test */
    public function cannot_delete_articles_owned_by_other_users()
    {
        $article = Article::factory()->create();

        Sanctum::actingAs(User::factory()->create());

        $this->deleteJson(route('api.v1.articles.destroy', $article))
                ->assertForbidden();

        $this->assertDatabaseCount('articles', 1);
    }
}
