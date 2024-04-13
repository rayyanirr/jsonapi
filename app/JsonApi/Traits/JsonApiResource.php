<?php

namespace App\JsonApi\Traits;

use App\Http\Resources\CategoryResource;
use App\JsonApi\Document;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

trait jsonApiResource
{
    abstract public  function toJsonApi(): array;

    public function toArray(Request $request): array
    {
        if ($request->filled('include')) {

            $this->with['included'] = $this->getIncludes();
        }

        return Document::type($this->getResourceType())
            ->id($this->resource->getRouteKey())
            ->attributes($this->filterAttributes($this->toJsonApi()))
            ->relationshipsLinks($this->getRelationshipLinks())
            ->links([
                'self' => route('api.v1.' . $this->getResourceType() . '.show', $this->resource),
            ])
            ->get('data');
    }

    public function getIncludes(): array
    {
        return [];
    }

    public function getRelationshipLinks(): array
    {
        return [];
    }

    public function withResponse(Request $request, JsonResponse $response)
    {
        $response->header(
            'Location',
            route('api.v1.' . $this->getResourceType() . '.show', $this->resource)
        );
    }

    public function filterAttributes(array $attributes): array
    {
        return array_filter($attributes, function ($value) {
            if (request()->isNotFilled('fields')) {
                return true;
            }
            $fields = explode(',', request('fields.' . $this->getResourceType()));
            if ($value === $this->getRouteKey()) {
                return in_array($this->getRouteKeyName(), $fields);
            }
            return $value;
        });
    }

    public static function collection($resources): AnonymousResourceCollection
    {
        $collection = parent::collection($resources);

        if (request()->filled('include')) {

            foreach ($resources as $resource) {

                foreach ($resource->getIncludes() as $include){

                    $collection->with['included'][] = $include;
                }
            }
        }


        $collection->with['links'] = ['self' => $resources->path()];

        return $collection;
    }
}
