<?php

namespace App\Http\Resources;

use App\JsonApi\Traits\jsonApiResource;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthorResource extends JsonResource
{
    use jsonApiResource;

    public function toJsonApi(): array
    {
        return [
            'name' => $this->resource->name,
        ];
    }
}
