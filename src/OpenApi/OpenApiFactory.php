<?php

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
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

        return $openApi;
    }
}