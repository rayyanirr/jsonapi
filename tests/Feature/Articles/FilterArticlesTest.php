<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FilterArticlesTest extends TestCase
{
    use RefreshDatabase;


    /** @test */
    public function can_filter_articles_by_content(): void
    {
        Article::factory()->create([
            'content' => 'Aprende Laravel Desde Cero'
        ]);

        Article::factory()->create([
            'content' => 'Other Article'
        ]);

        //articles?filter[content]=Laravel

        $url = route('api.v1.articles.index', [
            'filter' => [
                'content' => 'Laravel'
            ]
        ]);

        $this->getJson($url)
            ->assertJsonCount(1, 'data')
            ->assertSee('Aprende Laravel Desde Cero')
            ->assertDontSee('Other Article');
    }

    /** @test */
    public function can_filter_articles_by_title(): void
    {
        Article::factory()->create([
            'title' => 'Aprende Laravel Desde Cero'
        ]);

        Article::factory()->create([
            'title' => 'Other Article'
        ]);

        //articles?filter[year]=2021

        $url = route('api.v1.articles.index', [
            'filter' => [
                'title' => 'Laravel'
            ]
        ]);

        $this->getJson($url)
            ->assertJsonCount(1, 'data')
            ->assertSee('Aprende Laravel Desde Cero')
            ->assertDontSee('Other Article');
    }

    /** @test */
    public function can_filter_articles_by_year(): void
    {
        Article::factory()->create([
            'title' => 'Article from 2021',
            'created_at' => now()->year(2021)
        ]);

        Article::factory()->create([
            'title' => 'Article from 2022',
            'created_at' => now()->year(2022)
        ]);

        //articles?filter[title]=Laravel

        $url = route('api.v1.articles.index', [
            'filter' => [
                'year' => '2021'
            ]
        ]);



        $this->getJson($url)
            ->assertJsonCount(1, 'data')
            ->assertSee('Article from 2021')
            ->assertDontSee('Article from 2022');
    }

    /** @test */
    public function can_filter_articles_by_month(): void
    {
        Article::factory()->create([
            'title' => 'Article from month 3',
            'created_at' => now()->month(3)
        ]);

        Article::factory()->create([
            'title' => 'Article from month 11',
            'created_at' => now()->month(11)
        ]);

        //articles?filter[month]=3

        $url = route('api.v1.articles.index', [
            'filter' => [
                'month' => '3'
            ]
        ]);

        $this->getJson($url)
            ->assertJsonCount(1, 'data')
            ->assertSee('Article from month 3')
            ->assertDontSee('Article from month 11');
    }

    /** @test */
    public function can_filter_articles_by_category(): void
    {
        Article::factory()->count(2)->create();
        $cat1 = Category::factory()->hasArticles(3)->create(['slug' => 'cat1']);
        $cat2 = Category::factory()->hasArticles()->create(['slug' => 'cat2']);

        //articles?filter[month]=3

        $url = route('api.v1.articles.index', [
            'filter' => [
                'categories' => 'cat1,cat2'
            ]
        ]);

        $this->getJson($url)
            ->assertJsonCount(4, 'data')
            ->assertSee($cat1->articles[0]->title)
            ->assertSee($cat1->articles[1]->title)
            ->assertSee($cat1->articles[2]->title)
            ->assertSee($cat2->articles[0]->title);
    }


    /** @test */
    public function cannot_filter_articles_by_unknown_fields(): void
    {
        Article::factory()->count(3)->create();

        // /articles?filter=unknown

        $url = route('api.v1.articles.index', ['filter' => ['unknown' => 'unknown' ]]);

        $this->getJson($url)->assertJsonApiError(
            title: "Bad Request",
            detail: "the filter field 'unknown' is not allowed in the 'articles' resource",
            status: "400"
        );
    }


}
