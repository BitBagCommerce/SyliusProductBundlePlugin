<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusProductBundlePlugin\Api;

use Composer\InstalledVersions;

abstract class AdminJsonApiTestCase extends JsonApiTestCase
{
    public function getAuthToken(string $email, string $password): string
    {
        $syliusVersion = InstalledVersions::getVersion('sylius/sylius');

        if (version_compare($syliusVersion, '1.13.0', '>=')) {
            $endpoint = '/api/v2/admin/administrators/token';
        } else {
            $endpoint = '/api/v2/admin/authentication-token';
        }

        $this->client->request(
            'POST',
            $endpoint,
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
