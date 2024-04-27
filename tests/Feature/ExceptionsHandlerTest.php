<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ExceptionsHandlerTest extends TestCase
{
    use RefreshDatabase;


    /** @test */
    public function json_api_errors_are_only_shown_to_requests_with_the_prefix_api(): void
    {
        $this->getJson('api/route')
            ->assertJsonApiError(
                detail: 'The route api/route could not be found.',
                status: '404'
            );

        $this->getJson('api/v1/invalid-resource/invalid-id')
            ->assertJsonApiError(
                detail: 'The route api/v1/invalid-resource/invalid-id could not be found.',
                status: '404'
            );
    }
}
