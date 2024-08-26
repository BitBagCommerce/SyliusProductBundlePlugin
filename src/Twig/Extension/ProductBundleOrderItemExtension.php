<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Twig\Extension;

use BitBag\SyliusProductBundlePlugin\Entity\ProductInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class ProductBundleOrderItemExtension extends AbstractExtension
{
    public function __construct(
        private readonly RepositoryInterface $productBundleOrderItemRepository,
        private readonly Environment $twig,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('bitbag_render_admin_product_bundle_order_items', [$this, 'renderAdminProductBundleOrderItems'], ['is_safe' => ['html']]),
            new TwigFunction('bitbag_render_shop_product_bundle_order_items', [$this, 'renderShopProductBundleOrderItems'], ['is_safe' => ['html']]),
        ];
    }

    public function renderAdminProductBundleOrderItems(OrderItemInterface $orderItem): string
    {
        $items = $this->getItems($orderItem);

        if ($items === []) {
            return '';
        }

        return $this->twig->render('@BitBagSyliusProductBundlePlugin/Admin/Order/Show/_productBundleOrderItems.html.twig', [
            'items' => $items,
        ]);
    }

    public function renderShopProductBundleOrderItems(OrderItemInterface $orderItem): string
    {
        $items = $this->getItems($orderItem);

        if ($items === []) {
            return '';
        }

        return $this->twig->render('@BitBagSyliusProductBundlePlugin/Shop/Order/Show/_productBundleOrderItems.html.twig', [
            'items' => $items,
        ]);
    }

    private function getItems(OrderItemInterface $orderItem): array
    {
        /** @var ProductInterface $product */
        $product = $orderItem->getProduct();

        if (!$product->isBundle()) {
            return [];
        }

        return $this->productBundleOrderItemRepository->findBy([
            'orderItem' => $orderItem,
        ]);
    }
}
