<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusProductBundlePlugin\Api;

use ApiTestCase\JsonApiTestCase as BaseJsonApiTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class JsonApiTestCase extends BaseJsonApiTestCase
{
    public const CONTENT_TYPE_HEADER = ['CONTENT_TYPE' => 'application/ld+json', 'HTTP_ACCEPT' => 'application/ld+json'];

    protected static function getContainer(): ContainerInterface
    {
        if (is_callable('parent::getContainer')) {
            return parent::getContainer();
        }

        return self::$container;
    }
}
