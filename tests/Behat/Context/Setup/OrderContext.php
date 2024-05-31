<?php

declare(strict_types=1);

namespace Tests\BitBag\SyliusProductBundlePlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleOrderItem;
use BitBag\SyliusProductBundlePlugin\Factory\ProductBundleOrderItemFactoryInterface;
use Doctrine\Persistence\ObjectManager;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class OrderContext implements Context
{
    public function __construct(
        private SharedStorageInterface $sharedStorage,
        private ProductBundleOrderItemFactoryInterface $bundleOrderItemFactory,
        private ObjectManager $objectManager,
    )
    {
    }

    /**
     * @Given /^bundled products are bound to the order$/
     */
    public function bundledProductsAreBoundToTheOrder()
    {
        /** @var OrderInterface $order */
        $order = $this->sharedStorage->get('product');
        /** @var ProductBundleOrderItem $bundle */
        $bundle = $order->getItems()->first();

        $this->bundleOrderItemFactory->createFromProductBundleItem($bundle->getProductBundleItem());

        $this->objectManager->flush();

        dd();
    }
}
