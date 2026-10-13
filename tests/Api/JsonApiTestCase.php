<?php

/*
 * This file is part of the Sylius ProductBundle Plugin package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Sylius\ProductBundlePlugin\Api;

use ApiTestCase\JsonApiTestCase as BaseJsonApiTestCase;
use Composer\InstalledVersions;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Response;

abstract class JsonApiTestCase extends BaseJsonApiTestCase
{
    public const DEFAULT_HEADER = ['CONTENT_TYPE' => 'application/ld+json', 'HTTP_ACCEPT' => 'application/ld+json'];

    public const PATCH_HEADER = ['CONTENT_TYPE' => 'application/merge-patch+json', 'HTTP_ACCEPT' => 'application/ld+json'];

    protected static function getContainer(): Container
    {
        $parentClass = get_parent_class(static::class);

        if ($parentClass !== false && method_exists($parentClass, 'getContainer')) {
            return parent::getContainer();
        }

        return self::$container;
    }

    protected function assertResponseWithVersionSupport(
        Response $response,
        string $responsePath,
        int $statusCode = Response::HTTP_OK,
    ): void {
        $syliusVersion = InstalledVersions::getVersion('sylius/sylius');
        $majorMinor = implode('.', array_slice(explode('.', $syliusVersion), 0, 2));

        $versionSpecificPath = $responsePath . '_' . $majorMinor;
        $file = __DIR__ . '/Responses/' . $versionSpecificPath . '.json';

        if (!file_exists($file)) {
            $this->assertResponse($response, $responsePath, $statusCode);

            return;
        }

        $this->assertResponse($response, $versionSpecificPath, $statusCode);
    }
}
