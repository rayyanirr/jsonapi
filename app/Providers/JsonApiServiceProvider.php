<?php

namespace App\Providers;

use App\JsonApi\JsonApiQueryBuilder;
use App\JsonApi\JsonApiRequest;
use App\JsonApi\JsonApiTestResponse;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Testing\TestResponse;

class JsonApiServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {

        Builder::mixin(new JsonApiQueryBuilder());

        TestResponse::mixin(new JsonApiTestResponse());

        Request::mixin(new JsonApiRequest());

    }
}
