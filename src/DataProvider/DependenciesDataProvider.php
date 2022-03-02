<?php

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Dependencies;
use Ramsey\Uuid\Uuid;

class DependenciesDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface,
    ItemDataProviderInterface
{
    public function __construct(private string $rootPath)
    {
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Dependencies::class === $resourceClass;
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = []): iterable
    {
        $searchQuery = $context['filters']['search'] ?? '';

        $dependencies = $this->getDependencies();
        $collection = [];
        foreach ($dependencies as $name => $version) {
            $uuid = Uuid::uuid5(Uuid::NAMESPACE_URL, $name)->toString();
            if ($searchQuery) {
                if (str_contains($name, $searchQuery)) {
                    $collection[] = new Dependencies($uuid, $name, $version);
                }
            } else {
                $collection[] = new Dependencies($uuid, $name, $version);
            }
        }

        return $collection;
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = []): ?object
    {
        $dependencies = $this->getDependencies();
        foreach ($dependencies as $name => $version) {
            $uuid = Uuid::uuid5(Uuid::NAMESPACE_URL, $name)->toString();
            if ($uuid === $id) {
                return new Dependencies($uuid, $name, $version);
            }
        }
        return null;
    }

    private function getDependencies(): array
    {
        $file = $this->rootPath . '/composer.json';
        $fileDecode = json_decode(file_get_contents($file), true);
        return $fileDecode['require'] ?? [];
    }
}