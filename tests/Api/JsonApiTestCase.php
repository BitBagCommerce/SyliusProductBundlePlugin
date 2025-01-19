<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusProductBundlePlugin\Api;

use ApiTestCase\JsonApiTestCase as BaseJsonApiTestCase;
use Symfony\Component\DependencyInjection\Container;

abstract class JsonApiTestCase extends BaseJsonApiTestCase
{
    public const DEFAULT_HEADER = ['CONTENT_TYPE' => 'application/ld+json', 'HTTP_ACCEPT' => 'application/ld+json'];

    public const PATCH_HEADER = ['CONTENT_TYPE' => 'application/merge-patch+json', 'HTTP_ACCEPT' => 'application/ld+json'];

    protected static function getContainer(): Container
    {
        if (is_callable('parent::getContainer')) {
            /* @phpstan-ignore-next-line */
            return parent::getContainer();
        }

        return self::$container;
    }
}
