<?php

declare(strict_types=1);

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\Model\RequestBody;
use ApiPlatform\Core\OpenApi\OpenApi;
use ArrayObject;

final class LoginDecorator implements OpenApiFactoryInterface
{
    public function __construct(private OpenApiFactoryInterface $decorated)
    {
    }

    /**
     * @param array<string, string> $context
     */
    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);
        $schemas = $openApi->getComponents()->getSchemas();

        $schemas['Token'] = $this->getSchemaToken();
        $schemas['Credentials'] = $this->getSchemaCredentials();

        $pathItem = $this->getPathItem();
        $openApi->getPaths()->addPath('/api/login_check', $pathItem);

        return $openApi;
    }

    private function getPathItem(): PathItem
    {
        return new PathItem(
            ref: 'JWT Token',
            post: new Operation(
                operationId: 'postCredentialsItem',
                tags: ['Token'],
                responses: [
                    '200' => [
                        'description' => 'Get JWT token',
                        'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/Token']]],
                    ],
                ],
                summary: 'Get JWT token to login.',
                requestBody: new RequestBody(
                    description: 'Generate new JWT Token',
                    content: new ArrayObject([
                        'application/json' => ['schema' => ['$ref' => '#/components/schemas/Credentials']],
                    ]),
                ),
            ),
        );
    }

    /**
     * @return ArrayObject<string, array{email: array{type: string, example: string}, password: array{type: string, example:string}}|string>
     */
    private function getSchemaCredentials(): ArrayObject
    {
        return new ArrayObject([
            'type' => 'object',
            'properties' => [
                'email' => [
                    'type' => 'string',
                    'example' => 'user+1@email.com',
                ],
                'password' => [
                    'type' => 'string',
                    'example' => 'password',
                ],
            ],
        ]);
    }

    /**
     * @return ArrayObject<string, array{token: array{type: string, readOnly: bool}}|string>
     */
    private function getSchemaToken(): ArrayObject
    {
        return new ArrayObject([
            'type' => 'object',
            'properties' => [
                'token' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
            ],
        ]);
    }
}
