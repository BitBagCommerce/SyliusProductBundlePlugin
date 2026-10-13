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

use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Resource\Model\TimestampableTrait;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class ProductBundleItem implements ProductBundleItemInterface
{
    use TimestampableTrait;

    protected mixed $id = null;

    #[Assert\NotBlank(message: 'sylius_product_bundle.product_bundle_item.product_variant.not_blank', groups: ['sylius_product_bundle'])]
    #[Groups(['admin:product_bundle:create', 'admin:product_bundle:update', 'shop:product:read', 'shop:product_bundle:read'])]
    protected ?ProductVariantInterface $productVariant = null;

    #[Assert\NotBlank(message: 'sylius_product_bundle.product_bundle_item.quantity.not_blank', groups: ['sylius_product_bundle'])]
    #[Assert\Positive(groups: ['sylius_product_bundle'])]
    #[Groups(['admin:product_bundle:create', 'admin:product_bundle:update', 'shop:product:read', 'shop:product_bundle:read'])]
    protected ?int $quantity = null;

    protected ?ProductBundleInterface $productBundle = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProductVariant(): ?ProductVariantInterface
    {
        return $this->productVariant;
    }

    public function setProductVariant(?ProductVariantInterface $productVariant): void
    {
        $this->productVariant = $productVariant;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function getProductBundle(): ?ProductBundleInterface
    {
        return $this->productBundle;
    }

    public function setProductBundle(?ProductBundleInterface $productBundle): void
    {
        $this->productBundle = $productBundle;
    }
}
