<?php

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\Model\RequestBody;
use ApiPlatform\Core\OpenApi\OpenApi;

class OpenApiFactory implements OpenApiFactoryInterface
{
    public function __construct(private OpenApiFactoryInterface $decorated)
    {
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi =  $this->decorated->__invoke($context);

        // https://swagger.io/docs/specification/authentication/cookie-authentication/
        $securitySchemes = $openApi->getComponents()->getSecuritySchemes();
        $securitySchemes['cookieAuth'] = new \ArrayObject([
            'type' => 'apiKey',
            'in' => 'cookie',
            'name' => 'JSESSIONID'
        ]);

        $loginPath = new PathItem();
        $loginOperation = new Operation(
            operationId: 'apiAuthLogin',
            tags: ['Security'],
            responses: [
                '200' => [
                    'description' => 'authentication success',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'email' => [
                                        'type' => 'string'
                                    ],
                                    'iri' => [
                                        'type' => 'string'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                '401' => [
                    'description' => 'authentication failure',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'error' => [
                                        'type' => 'string'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            summary: 'Authentication',
            description: 'Authentication',
            requestBody: new RequestBody(
                content: new \ArrayObject([
                    'application/json' => [
                        'schema' => [
                            "type" => "object",
                            'properties' => [
                                'email' => [
                                    'type' => 'string',
                                    'example' => 'cheeselover1@example.com'
                                ],
                                'password' => [
                                    'type' => 'string',
                                    'example' => 'foo'
                                ]
                            ]
                        ]
                    ]
                ])
            )
        );

        $openApi->getPaths()->addPath("/login", $loginPath->withPost($loginOperation));

        return $openApi;
    }
}