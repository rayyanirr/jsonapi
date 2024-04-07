<?php

namespace App\Http\Resources;

use App\JsonApi\Traits\jsonApiResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    use jsonApiResource;

    public function toJsonApi(): array
    {
        return [
            'name' => $this->resource->name,
            'slug' => $this->resource->slug,
        ];
    }
}
