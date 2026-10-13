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

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\SerializedName;

trait ProductBundlesAwareTrait
{
    #[ORM\OneToOne(mappedBy: 'product', targetEntity: ProductBundleInterface::class, cascade: ['all'])]
    #[Groups(['sylius:admin:product:index', 'sylius:admin:product:show', 'sylius:admin:product:create', 'sylius:admin:product:update', 'sylius:shop:product:index', 'sylius:shop:product:show'])]
    #[SerializedName('bundle')]
    protected ?ProductBundleInterface $productBundle = null;

    public function getProductBundle(): ?ProductBundleInterface
    {
        return $this->productBundle;
    }

    public function setProductBundle(?ProductBundleInterface $productBundle): void
    {
        $this->productBundle = $productBundle;
    }

    public function isBundle(): bool
    {
        return null !== $this->getProductBundle();
    }
}
