<?php

namespace App\Filter;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractContextAwareFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\PropertyInfo\Type;

class MySearchFilter extends AbstractContextAwareFilter
{
    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
        if($property !== 'search') {
            return;
        }

        $searchValues = [];

        if (str_contains($value, ',')) {
            $searchValues = array_map('trim', explode(',', $value));
        } else {
            $searchValues[] = trim($value);
        }

        $searchValues = $this->removeEmptySearchValue($searchValues);
         if (!empty($searchValues)) {
             $this->buildQuery($queryBuilder, $queryNameGenerator, $searchValues);
         }
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param QueryNameGeneratorInterface $queryNameGenerator
     * @param array $values
     * @return void
     */
    private function buildQuery(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, array $values): void
    {
        $alias = $queryBuilder->getRootAliases()[0];
        $orExp = $queryBuilder->expr()->orX();
        foreach ($values as $value) {
            $parameterName = $queryNameGenerator->generateParameterName('search');
            $orExp->add($queryBuilder->expr()->like('LOWER('. $alias. '.title)', ':' . $parameterName));
            $queryBuilder->setParameter($parameterName, '%' . strtolower($value). '%');
        }
        $queryBuilder->andWhere($orExp);
    }

    /**
     * @param array $searchValues
     * @return array
     */
    private function removeEmptySearchValue(array $searchValues): array
    {
        return array_filter($searchValues, fn ($value) => !is_null($value) && $value !== '');
    }

    public function getDescription(string $resourceClass): array
    {
        return [
            'search' => [
                'property' => null,
                'type' => Type::BUILTIN_TYPE_STRING,
                'required' => false,
            ]
        ];
    }
}