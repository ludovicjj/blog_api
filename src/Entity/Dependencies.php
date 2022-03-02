<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Filter\DependenciesFilter;

#[ApiResource(
    collectionOperations: ['get'],
    itemOperations: ['get'],
    paginationEnabled: false
)]
#[ApiFilter(DependenciesFilter::class)]
class Dependencies
{
    #[ApiProperty(identifier: true)]
    private string $uuid;

    #[ApiProperty(
        description: 'Nom de la dependence',
        example: "api-platform/core"
    )]
    private string $name;

    #[ApiProperty(
        description: 'Version de la dependence',
        example: "^2.6"
    )]
    private string $version;

    public function __construct($uuid, $name, $version)
    {
        $this->uuid = $uuid;
        $this->name = $name;
        $this->version = $version;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getVersion(): string
    {
        return $this->version;
    }
}