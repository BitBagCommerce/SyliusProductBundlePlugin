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

namespace Sylius\ProductBundlePlugin\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

trait ProductBundleOrderItemsAwareTrait
{
    /** @var ProductBundleOrderItemInterface[]|Collection */
    #[ORM\OneToMany(mappedBy: 'orderItem', targetEntity: ProductBundleOrderItem::class, cascade: ['all'])]
    #[Groups(['admin:order:read', 'admin:order_item:read', 'shop:order_item:read', 'shop:cart:read'])]
    protected Collection $productBundleOrderItems;

    protected function initializeProductBundleOrderItems(): void
    {
        $this->productBundleOrderItems = new ArrayCollection();
    }

    /** @return ProductBundleOrderItemInterface[]|Collection */
    public function getProductBundleOrderItems(): Collection
    {
        return $this->productBundleOrderItems;
    }

    public function addProductBundleOrderItem(ProductBundleOrderItemInterface $productBundleOrderItem): void
    {
        $this->productBundleOrderItems->add($productBundleOrderItem);
        $productBundleOrderItem->setOrderItem($this);
    }
}
