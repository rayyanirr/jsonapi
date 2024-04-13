<?php

namespace App\Http\Resources;

use App\JsonApi\Traits\jsonApiResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    use jsonApiResource;

    public function toJsonApi(): array
    {
        return [
            'title' => $this->resource->title,
            'slug' => $this->resource->slug,
            'content' => $this->resource->content,
        ];
    }

    public function getRelationshipLinks(): array  {

        return ['category'];
    }

    public function getIncludes(): array  {

        return [

            CategoryResource::make($this->resource->category)

        ];
    }


}
