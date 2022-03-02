<?php

namespace App\Filter;

use ApiPlatform\Core\Serializer\Filter\FilterInterface;
use Symfony\Component\HttpFoundation\Request;

class DependenciesFilter implements FilterInterface
{
    public function apply(Request $request, bool $normalization, array $attributes, array &$context): void
    {
    }

    public function getDescription(string $resourceClass): array
    {
        return [
            'search' => [
                'property' => null,
                'type' => 'string',
                'required' => false,
                'description' => 'Search dependencies contain value',
                'schema' => [
                    'type' => 'string',
                    'example' => 'php'
                ],
                'openapi' => [
                    'description' => 'Search dependencies contain value',
                ],
            ]
        ];
    }
}