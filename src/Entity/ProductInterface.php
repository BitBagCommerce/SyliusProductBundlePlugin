<?php

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Entity;

use Sylius\Component\Core\Model\ProductInterface as BaseProductInterface;

interface ProductInterface extends ProductBundlesAwareInterface, BaseProductInterface
{
}
