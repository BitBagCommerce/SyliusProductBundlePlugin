<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Provider;

use BitBag\SyliusProductBundlePlugin\Command\AddProductBundleItemToCartCommandInterface;
use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleItemInterface;
use BitBag\SyliusProductBundlePlugin\Factory\AddProductBundleItemToCartCommandFactoryInterface;
use BitBag\SyliusProductBundlePlugin\Repository\ProductBundleRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;

final class AddProductBundleItemToCartCommandProvider implements AddProductBundleItemToCartCommandProviderInterface
{
    public const TO = 'to';

    public const FROM = 'from';

    public function __construct(
        private readonly AddProductBundleItemToCartCommandFactoryInterface $addProductBundleItemToCartCommandFactory,
        private readonly ProductBundleRepositoryInterface $productBundleRepository,
        private readonly ProductVariantRepositoryInterface $productVariantRepository,
    ) {
    }

    /**
     * @return Collection<int, AddProductBundleItemToCartCommandInterface>
     *
     * @throws \Exception
     */
    public function provide(string $bundleCode, array $overwrittenVariants): Collection
    {
        $bundle = $this->productBundleRepository->findOneByProductCode($bundleCode);
        if (null === $bundle) {
            throw new \Exception('Product bundle not found');
        }

        $bundleItems = $bundle->getProductBundleItems();
        $commands = [];
        foreach ($bundleItems as $bundleItem) {
            $command = $this->addProductBundleItemToCartCommandFactory->createNew($bundleItem);
            if (!$bundle->isPackedProduct() && [] !== $overwrittenVariants) {
                $this->overwriteVariant($command, $bundleItem, $overwrittenVariants);
            }
            $commands[] = $command;
        }

        return new ArrayCollection($commands);
    }

    private function overwriteVariant(
        AddProductBundleItemToCartCommandInterface $command,
        ProductBundleItemInterface $bundleItem,
        array $overwrittenVariants,
    ): void {
        foreach ($overwrittenVariants as $overwrittenVariant) {
            if (null !== $overwrittenVariant[self::FROM] && null !== $overwrittenVariant[self::TO] &&
                $bundleItem->getProductVariant()?->getCode() === $overwrittenVariant[self::FROM] &&
                $this->shouldOverwriteVariant($overwrittenVariant[self::FROM], $overwrittenVariant[self::TO])
            ) {
                /** @var ProductVariantInterface $newVariant */
                $newVariant = $this->productVariantRepository->findOneBy(['code' => $overwrittenVariant[self::TO]]);
                $command->setProductVariant($newVariant);
            }
        }
    }

    private function shouldOverwriteVariant(string $oldVariantCode, string $newVariantCode): bool
    {
        $oldVariant = $this->productVariantRepository->findOneBy(['code' => $oldVariantCode]);
        $newVariant = $this->productVariantRepository->findOneBy(['code' => $newVariantCode]);

        return
            $oldVariant instanceof ProductVariantInterface &&
            $newVariant instanceof ProductVariantInterface &&
            $oldVariant->getProduct() === $newVariant->getProduct();
    }
}
