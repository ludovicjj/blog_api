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

        // Update summary to get operation "/api/post/count"
        /** @var PathItem $path */
        foreach ($openApi->getPaths()->getPaths() as $key => $path) {
            if ($path->getGet() && str_contains($path->getGet()->getSummary(), 'Permet')) {
                $operation = $path->getGet();
                $openApi->getPaths()->addPath(
                    $key, $path->withGet($operation->withSummary($operation->getSummary() .' (modifié)'))
                );
            }
        }

        // Update responses to get operation "/api/posts/{id}"
        $path = $openApi->getPaths()->getPath('/api/posts/{id}');
        $operation = $path->getGet();
        $openApi->getPaths()->addPath(
            '/api/posts/{id}',
            $path->withGet(
                $operation->withResponses(
                    array_filter($operation->getResponses(), fn($status) => $status !== 204, ARRAY_FILTER_USE_KEY)
                )
            )
        );

        // add query parameter to get operation "/api/post/count"
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