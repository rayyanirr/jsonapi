<?php

namespace App\JsonApi\Traits;

use App\JsonApi\Document;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

trait JsonApiResource
{
    abstract public function toJsonApi(): array;

    public function toArray(Request $request): array
    {
        if ($request->filled('include')) {

            foreach ($this->getIncludes() as $include) {

                if ($include->resource instanceof MissingValue) {

                    continue;
                }
                $this->with['included'][] = $include;
            }
        }

        return Document::type($this->resource->getResourceType())
            ->id($this->resource->getRouteKey())
            ->attributes($this->filterAttributes($this->toJsonApi()))
            ->relationshipsLinks($this->getRelationshipLinks())
            ->links([
                'self' => route('api.v1.' . $this->resource->getResourceType() . '.show', $this->resource),
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

                foreach ($resource->getIncludes() as $include) {

                    if ($include->resource instanceof MissingValue) {

                        continue;
                    }
                    $collection->with['included'][] = $include;
                }
            }
        }

        $collection->with['links'] = ['self' => request()->path()];

        return $collection;
    }

    public static function identifier(Model $resource): array
    {
        return Document::type($resource->getResourceType())
            ->id(
                $resource->getRouteKey()
            )->toArray();
    }

    public static function identifiers(Collection $resources): array
    {

        return $resources->isEmpty() ? Document::empty() : Document::type($resources->first()->getResourceType())
            ->ids($resources)->toArray();
    }
}
