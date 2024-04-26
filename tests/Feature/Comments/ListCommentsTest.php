<?php

namespace Tests\Feature\Comments;

use Tests\TestCase;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListCommentsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_fetch_a_single_comment()
    {
        $comment = Comment::factory()->create(['body' => 'Comment body']);

        $response = $this->getJson(route('api.v1.comments.show', $comment));

        $response->assertJsonApiResource($comment, [
            'body' => 'Comment body',
        ]);

        $response->assertJsonApiRelationshipsLinks($comment, ['article', 'author']);
    }

    /** @test */
    public function can_fetch_all_comments()
    {
        $this->withoutExceptionHandling();

        $comments = Comment::factory()->count(3)->create();

        $response = $this->getJson(route('api.v1.comments.index'));

        $response->assertJsonApiResourceCollection($comments, [
            'body',
        ]);
    }

    /** @test */
    public function it_returns_a_json_api_error_object_when_an_comment_is_not_found()
    {

        $response = $this->getJson(route('api.v1.comments.show', 'not-existing'));

        $response->assertJsonApiError(
            title: 'Not Found',
            detail: "No records found with the id 'not-existing' in the 'comments' resource.",
            status: '404'
        );
    }
}
