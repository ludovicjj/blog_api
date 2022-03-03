<?php

namespace App\Filter;

use ApiPlatform\Core\Serializer\Filter\FilterInterface;
use Symfony\Component\HttpFoundation\Request;

class DailyStatsFilter implements FilterInterface
{

    public function apply(Request $request, bool $normalization, array $attributes, array &$context)
    {
        // TODO: Implement apply() method.
    }

    public function getDescription(string $resourceClass): array
    {
        return [
            'from' => [
                'property' => null,
                'type' => 'string',
                'required' => false,
                'description' => 'From date e.g. 2020-09-01',
                'openapi' => [
                    'description' => 'From date e.g. 2020-09-01',
                ],
            ]
        ];
    }
}