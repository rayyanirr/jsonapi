<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryRelationshipTest extends TestCase
{
    use RefreshDatabase;


    /** @test */
    public function can_fetch_the_associated_category_identifier(): void
    {
        $article = Article::factory()->create();

        $url = route('api.v1.articles.relationships.category', $article);

        $response = $this->getJson($url);

        $response->assertExactJson([
            'data' => [

                'id' => $article->category->getRouteKey(),
                'type' => 'categories'
            ]
        ]);
    }

    /** @test */
    public function can_fetch_the_associated_category_resource(): void
    {
        $article = Article::factory()->create();

        $url = route('api.v1.articles.category', $article);

        $response = $this->getJson($url);

        $response->assertJson([
            'data' => [
                'id' => $article->category->getRouteKey(),
                'type' => 'categories',
                'attributes' => [
                    'name' => $article->category->name
                ],
            ]
        ]);
    }


    /** @test */
    public function can_update_the_associated_category(): void
    {
        $article = Article::factory()->create();
        $category = Category::factory()->create();

        $url = route('api.v1.articles.relationships.category.update', $article);



        $response = $this->patchJson($url, [
            'data' => [
                'type' => 'categories',
                'id' => $category->getRouteKey()
            ]
        ]);

        $response->assertExactJson([

            'data' => [
                'type' => 'categories',
                'id' => $category->getRouteKey()
            ]
        ]);

        $this->assertDatabaseHas('articles',[

            'title' => $article->title,
            'category_id' => $category->id
        ]);

    }

     /** @test */
     public function category_must_exist_in_database(): void
     {
         $article = Article::factory()->create();

         $url = route('api.v1.articles.relationships.category.update', $article);


         $this->patchJson($url, [
             'data' => [
                 'type' => 'categories',
                 'id' =>'non-existing'
             ]
         ])->assertJsonApiValidationErrors('data.id');



         $this->assertDatabaseHas('articles',[

             'title' => $article->title,
             'category_id' => $article->category_id
         ]);

     }


}
