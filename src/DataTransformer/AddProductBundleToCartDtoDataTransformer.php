<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use BitBag\SyliusProductBundlePlugin\Command\AddProductBundleToCartCommand;
use BitBag\SyliusProductBundlePlugin\Dto\Api\AddProductBundleToCartDto;
use Sylius\Component\Order\Model\OrderInterface;
use Webmozart\Assert\Assert;

final class AddProductBundleToCartDtoDataTransformer implements DataTransformerInterface
{
    public const OBJECT_TO_POPULATE = 'object_to_populate';

    /**
     * @param AddProductBundleToCartDto|object $object
     */
    public function transform(
        $object,
        string $to,
        array $context = []
    ): AddProductBundleToCartCommand {
        Assert::isInstanceOf($object, AddProductBundleToCartDto::class);

        /** @var OrderInterface|null $cart */
        $cart = $context[self::OBJECT_TO_POPULATE] ?? null;
        Assert::notNull($cart);

        $productCode = $object->getProductCode();
        $quantity = $object->getQuantity();

        return new AddProductBundleToCartCommand($cart->getId(), $productCode, $quantity);
    }

    public function supportsTransformation(
        $data,
        string $to,
        array $context = []
    ): bool {
        return isset($context['input']['class']) && AddProductBundleToCartDto::class === $context['input']['class'];
    }
}
