<?php

namespace App\Filter;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractContextAwareFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

class MySearchFilter extends AbstractContextAwareFilter
{
    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
        if($property !== 'search') {
            return;
        }

        $searchValues = [];

        if (str_contains($value, ',')) {
            $searchValues = explode(',', $value);
        } else {
            $searchValues[] = $value;
        }

        foreach ($searchValues as $index => $searchValue) {
            $this->buildAndWhere(
                $queryBuilder,
                $index,
                $searchValue,
                $queryNameGenerator->generateParameterName('search')
            );
        }
    }

    private function buildAndWhere(QueryBuilder $queryBuilder, $index, $value, string $parameterName)
    {
        $alias = $queryBuilder->getRootAliases()[0];
        $orExp = $queryBuilder->expr()->orX(
            $queryBuilder->expr()->like('LOWER('. $alias. '.title)', ':' . $parameterName)
        );

        if ($index > 0) {
            $queryBuilder->orWhere($orExp);
        } else {
            $queryBuilder->andWhere($orExp);
        }
        $queryBuilder->setParameter($parameterName, '%' . strtolower($value). '%');
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