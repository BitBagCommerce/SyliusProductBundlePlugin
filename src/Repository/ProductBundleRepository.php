<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Repository;

use Doctrine\Common\Collections\Collection;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Product\Model\ProductVariantInterface;

class ProductBundleRepository extends EntityRepository implements ProductBundleRepositoryInterface
{
    public function getProductIds(): array
    {
        return $this->createQueryBuilder('pb')
            ->select('product.id')
            ->leftJoin('pb.product', 'product')
            ->leftJoin('pb.productBundleItems', 'items')
            ->andWhere('items.id IS NOT NULL')
            ->getQuery()
            ->getSingleColumnResult();
    }

    /** @param Collection<int, ProductVariantInterface> $variants */
    public function findBundlesByVariants(Collection $variants): array
    {
        return $this->createQueryBuilder('pb')
            ->select('pb')
            ->leftJoin('pb.productBundleItems', 'items')
            ->andWhere('items.productVariant IN (:variants)')
            ->setParameter('variants', $variants)
            ->getQuery()
            ->getResult();
    }
}
