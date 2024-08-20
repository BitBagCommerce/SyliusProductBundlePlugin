<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Fixture;

use Sylius\Bundle\CoreBundle\Fixture\AbstractResourceFixture;
use Sylius\Bundle\FixturesBundle\Fixture\FixtureInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

final class ProductBundleFixture extends AbstractResourceFixture implements FixtureInterface
{
    protected function configureResourceNode(ArrayNodeDefinition $resourceNode): void
    {
        $resourceNodeChildren = $resourceNode->children();
        $resourceNodeChildren->scalarNode('bundle')->end();
        $resourceNodeChildren->arrayNode('items')->scalarPrototype()->end();
        $resourceNodeChildren->booleanNode('is_packed')->end();
    }

    public function getName(): string
    {
        return 'product_bundle';
    }
}
