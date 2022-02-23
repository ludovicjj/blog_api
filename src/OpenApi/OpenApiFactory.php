<?php

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model\Parameter;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\OpenApi;

class OpenApiFactory implements OpenApiFactoryInterface
{
    public function __construct(private OpenApiFactoryInterface $decorated)
    {
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = $this->decorated->__invoke($context);
        /** @var PathItem $path */
        foreach ($openApi->getPaths()->getPaths() as $key => $path) {
            if ($path->getGet() && str_contains($path->getGet()->getSummary(), 'Permet')) {
                $operation = $path->getGet();
                $openApi->getPaths()->addPath(
                    $key, $path->withGet($operation->withSummary($operation->getSummary() .' (modifié)'))
                );
            }
        }

        $pathItem = $openApi->getPaths()->getPath('/api/posts/count');
        $operation = $pathItem->getGet();
        $openApi->getPaths()->addPath(
            '/api/posts/count',
            $pathItem->withGet(
                $operation->withParameters(
                    array_merge(
                        $operation->getParameters(),
                        [new Parameter(
                            'fields[]',
                            'query',
                            'Testing decorator',
                            false,
                            false,
                            true,
                            ['type' => 'array', 'items' => ['type' => 'string']],
                            'form',
                            true
                        )]
                    )
                )
            )
        );
        return $openApi;
    }
}