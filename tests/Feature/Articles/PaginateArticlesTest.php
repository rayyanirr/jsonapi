<?php

namespace Tests\Feature\Articles;

use Tests\TestCase;
use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PaginateArticlesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_paginate_articles(): void
    {
        $articles = Article::factory()->count(6)->create();

        //articles?page[size]=2&page[number]=2

        $url = route('api.v1.articles.index', [
            'page' => [
                'size' => 2,
                'number' => 2,
            ],
        ]);

        $response = $this->getJson($url);

        $response->assertSee([
            $articles[2]->title,
            $articles[3]->title,

        ]);
        $response->assertDontSee([
            $articles[0]->title,
            $articles[1]->title,
            $articles[4]->title,
            $articles[5]->title,
        ]);
        $response->assertJsonStructure([
            'links' => ['first', 'last', 'prev', 'next'],
        ]);

        $firstLink = urldecode($response->json('links.first'));
        $lastLink = urldecode($response->json('links.last'));
        $prevLink = urldecode($response->json('links.prev'));
        $nextLink = urldecode($response->json('links.next'));

        $this->assertStringContainsString('page[size]=2', $firstLink);
        $this->assertStringContainsString('page[number]=1', $firstLink);

        $this->assertStringContainsString('page[size]=2', $lastLink);
        $this->assertStringContainsString('page[number]=3', $lastLink);

        $this->assertStringContainsString('page[size]=2', $prevLink);
        $this->assertStringContainsString('page[number]=1', $prevLink);

        $this->assertStringContainsString('page[size]=2', $nextLink);
        $this->assertStringContainsString('page[number]=3', $nextLink);

    }

    /** @test */
    public function can_paginate_filtered_articles(): void
    {
        Article::factory()->count(3)->create();
        Article::factory()->create(['title' => 'C laravel']);
        Article::factory()->create(['title' => 'B laravel']);
        Article::factory()->create(['title' => 'A laravel']);

        //articles?filter=[title]=laravel&page[size]=2&page[number]=2

        $url = route('api.v1.articles.index', [
            'filter[title]' => 'laravel',
            'page' => [
                'size' => 1,
                'number' => 2,
            ],
        ]);

        $response = $this->getJson($url);

        $firstLink = urldecode($response->json('links.first'));
        $lastLink = urldecode($response->json('links.last'));
        $prevLink = urldecode($response->json('links.prev'));
        $nextLink = urldecode($response->json('links.next'));

        $this->assertStringContainsString('filter[title]=laravel', $firstLink);
        $this->assertStringContainsString('filter[title]=laravel', $lastLink);
        $this->assertStringContainsString('filter[title]=laravel', $prevLink);
        $this->assertStringContainsString('filter[title]=laravel', $nextLink);

    }

    /** @test */
    public function can_paginate_sorted_articles(): void
    {
        Article::factory()->create(['title' => 'C title']);
        Article::factory()->create(['title' => 'B title']);
        Article::factory()->create(['title' => 'A title']);

        //articles?sort=title&page[size]=2&page[number]=2

        $url = route('api.v1.articles.index', [
            'sort' => 'title',
            'page' => [
                'size' => 1,
                'number' => 2,
            ],
        ]);

        $response = $this->getJson($url);

        $response->assertSee([
            'B title',

        ]);
        $response->assertDontSee([
            'A title',
            'C title',
        ]);

        $firstLink = urldecode($response->json('links.first'));
        $lastLink = urldecode($response->json('links.last'));
        $prevLink = urldecode($response->json('links.prev'));
        $nextLink = urldecode($response->json('links.next'));

        $this->assertStringContainsString('sort=title', $firstLink);
        $this->assertStringContainsString('sort=title', $lastLink);
        $this->assertStringContainsString('sort=title', $prevLink);
        $this->assertStringContainsString('sort=title', $nextLink);

    }
}
