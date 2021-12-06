<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusProductBundlePlugin\Api;

use ApiTestCase\JsonApiTestCase;

abstract class AdminJsonApiTestCase extends JsonApiTestCase
{
    public function getAuthToken(string $email, string $password): string
    {
        $this->client->request(
            'POST',
            '/api/v2/admin/authentication-token',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_ACCEPT' => 'application/json'],
            json_encode(['email' => $email, 'password' => $password])
        );

        return json_decode($this->client->getResponse()->getContent(), true)['token'];
    }

    public function getHeaders($authToken = null): array
    {
        $headers = ['CONTENT_TYPE' => 'application/ld+json', 'HTTP_ACCEPT' => 'application/ld+json'];

        if (null === $authToken) {
            return $headers;
        }

        $authorizationHeader = self::getContainer()->getParameter('sylius.api.authorization_header');
        $headers['HTTP_' . $authorizationHeader] = 'Bearer ' . $authToken;

        return $headers;
    }
}
