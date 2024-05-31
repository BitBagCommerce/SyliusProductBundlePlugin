<?php

declare(strict_types=1);

namespace Tests\BitBag\SyliusProductBundlePlugin\Behat\Page\Admin\ProductVariants;

use Sylius\Behat\Page\Admin\Crud\IndexPage as BaseIndexPage;
use Sylius\Component\Core\Model\ProductVariantInterface;

class IndexPage extends BaseIndexPage implements IndexPageInterface
{
    public function getRouteName(): string
    {
        return 'sylius_admin_product_variant_index';
    }

    public function getOnHoldQuantityFor(ProductVariantInterface $productVariant): int
    {
        return (int) $this->getElement('on_hold_quantity', ['%id%' => $productVariant->getId()])->getText();
    }

    public function getOnHandQuantityFor(ProductVariantInterface $productVariant): int
    {

        return (int) $this->getElement('on_hand_quantity', ['%id%' => $productVariant->getId()])->getText();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'on_hand_quantity' => '.onHand[data-product-variant-id="%id%"]',
            'on_hold_quantity' => '.onHold[data-product-variant-id="%id%"]',
        ]);
    }
}
