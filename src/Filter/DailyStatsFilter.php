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
        // TODO: Implement getDescription() method.
    }
}