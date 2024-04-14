<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class IncludeCategoryTest extends TestCase
{
    use RefreshDatabase;


    /** @test */
    public function can_include_related_category_of_an_article(): void
    {
        $article = Article::factory()->create();

        $url = route('api.v1.articles.show',[
            'article' =>$article,
            'include' => 'category'

        ]);

        $this->getJson($url)->assertJson([

            'included' => [
                [
                    'type' => 'categories',
                    'id' => $article->category->getRouteKey(),
                    'attributes' => [
                        'name' => $article->category->name,
                    ],

                ]
            ]
        ]);
    }

    /** @test */
    public function can_include_related_categories_of_multiple_article(): void
    {
        $article = Article::factory()->create()->load('category');
        $article1 = Article::factory()->create()->load('category');

        $url = route('api.v1.articles.index',[

            'include' => 'category'

        ]);

        $this->getJson($url)->assertJson([

            'included' => [
                [
                    'type' => 'categories',
                    'id' => $article->category->getRouteKey(),
                    'attributes' => [
                        'name' => $article->category->name,
                    ],

                ],
                [
                    'type' => 'categories',
                    'id' => $article1->category->getRouteKey(),
                    'attributes' => [
                        'name' => $article1->category->name,
                    ],

                ],
            ]
        ]);
    }

    /** @test */
    public function can_include_related_unknow_relationships(): void
    {
        $article = Article::factory()->create()->load('category');


        // articles/the-slug?include=unknown
        $url = route('api.v1.articles.show',[
            'article' => $article,
            'include' => 'unknown,unknown2'

        ]);

        $this->getJson($url)->assertStatus(400);

        // articles/the-slug?include=unknown
        $url = route('api.v1.articles.index',[
            'include' => 'unknown,unknown2'
        ]);

        $this->getJson($url)->assertStatus(400);
    }
}
