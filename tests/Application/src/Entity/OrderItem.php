<?php

declare(strict_types=1);

namespace Tests\BitBag\SyliusProductBundlePlugin\Entity;

use BitBag\SyliusProductBundlePlugin\Entity\OrderItemInterface;
use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleOrderItemsAwareTrait;
use Sylius\Component\Core\Model\OrderItem as BaseOrderItem;

class OrderItem extends BaseOrderItem implements OrderItemInterface
{

    use ProductBundleOrderItemsAwareTrait;

    public function __construct()
    {
        parent::__construct();
        $this->init();
    }
}
