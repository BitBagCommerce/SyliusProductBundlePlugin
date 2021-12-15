<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\DataTransformer;

use BitBag\SyliusProductBundlePlugin\Command\AddProductBundleToCartCommand;
use BitBag\SyliusProductBundlePlugin\Command\ProductBundleIdAwareInterface;
use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleInterface;
use Sylius\Bundle\ApiBundle\DataTransformer\CommandDataTransformerInterface;
use Webmozart\Assert\Assert;

final class ProductBundleIdAwareInputCommandDataTransformer implements CommandDataTransformerInterface
{
    private const OBJECT_TO_POPULATE_KEY = 'object_to_populate';

    /**
     * @param mixed $object
     */
    public function transform($object, string $to, array $context = []): ProductBundleIdAwareInterface
    {
        Assert::isInstanceOf($object, ProductBundleIdAwareInterface::class);

        /** @var ProductBundleIdAwareInterface $object */
        /** @var ProductBundleInterface $productBundle */
        $productBundle = $context[self::OBJECT_TO_POPULATE_KEY];

        $object->setProductBundleId($productBundle->getId());

        return $object;
    }

    /**
     * @param mixed $object
     */
    public function supportsTransformation($object): bool
    {
        return $object instanceof ProductBundleIdAwareInterface;
    }
}
