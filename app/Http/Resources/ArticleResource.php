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
            'created_at' => $this->resource->created_at?->toAtomString(),
            'updated_at' => $this->resource->updated_at?->toAtomString(),
        ];
    }

    public function getRelationshipLinks(): array
    {

        return ['category', 'author'];
    }

    public function getIncludes(): array
    {

        return [

            CategoryResource::make($this->whenLoaded('category')),
            AuthorResource::make($this->whenLoaded('author')),
            CommentResource::collection($this->whenLoaded('comments')),

        ];
    }
}
