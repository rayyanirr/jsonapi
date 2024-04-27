<?php

namespace tests;

use App\JsonApi\Document;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;

trait MakesJsonApiRequests
{
    protected bool $formatJsonApiDocument = true;

    protected bool $addJsonHeaders = true;

    public function withoutJsonApiDocumentFormatting(): self
    {
        $this->formatJsonApiDocument = false;

        return $this;
    }

    public function withoutJsonApiHeaders(): self
    {
        $this->addJsonHeaders = false;

        return $this;
    }

    public function withoutJsonApiHelpers(): self
    {

        $this->formatJsonApiDocument = true;
        $this->addJsonHeaders = true;

        return $this;
    }

    public function json($method, $uri, array $data = [], array $headers = [], $options = 0): TestResponse
    {

        if ($this->addJsonHeaders) {

            $headers['accept'] = 'application/vnd.api+json';

            if ($method === 'POST' || $method === 'PATCH') {
                $headers['content-type'] = 'application/vnd.api+json';
            }
        }

        if ($this->formatJsonApiDocument && ($method === 'POST' || $method === 'PATCH')) {

            if (! isset($data['data'])) {

                $formattedData = $this->getFormattedData($uri, $data);
            }
        }

        return parent::json($method, $uri, $formattedData ?? $data, $headers, $options);
    }

    protected function getFormattedData($uri, array $data): array
    {
        $path = parse_url($uri)['path'];
        $type = (string) Str::of($path)->after('api/v1/')->before('/');
        $id = (string) Str::of($path)->after($type)->replace('/', '');

        return Document::type($type)
            ->id($id)
            ->attributes($data)
            ->relationshipData($data['_relationships'] ?? [])
            ->toArray();
    }
}
