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
        private RepositoryInterface $productBundleOrderItemRepository,
        private Environment $twig,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('bitbag_render_product_bundle_order_items', [$this, 'renderProductBundleOrderItems'], ['is_safe' => ['html']]),
        ];
    }

    public function renderProductBundleOrderItems(OrderItemInterface $orderItem): string
    {
        /** @var ProductInterface $product */
        $product = $orderItem->getProduct();

        if (!$product->isBundle()) {
            return '';
        }

        $items = $this->productBundleOrderItemRepository->findBy([
            'orderItem' => $orderItem,
        ]);

        return $this->twig->render('@BitBagSyliusProductBundlePlugin/Admin/Order/Show/_productBundleOrderItems.html.twig', [
            'items' => $items,
        ]);
    }
}
