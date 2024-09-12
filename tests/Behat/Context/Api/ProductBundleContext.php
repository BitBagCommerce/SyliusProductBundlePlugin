<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusProductBundlePlugin\Behat\Context\Api;

use Behat\Behat\Context\Context;
use BitBag\SyliusProductBundlePlugin\Entity\ProductInterface;
use BitBag\SyliusProductBundlePlugin\Provider\AddProductBundleItemToCartCommandProvider;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\RequestFactoryInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Context\Api\Resources;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Webmozart\Assert\Assert;

final class ProductBundleContext implements Context
{
    public function __construct(
        private readonly SharedStorageInterface $sharedStorage,
        private readonly ApiClientInterface $client,
        private readonly RequestFactoryInterface $requestFactory,
        private readonly ResponseCheckerInterface $responseChecker,
    ) {
    }

    /**
     * @When I add bundle :product to my cart
     * @When I add bundle :product with quantity :quantity to my cart
     */
    public function iAddProductBundleToMyCart(ProductInterface $product, int $quantity = 1): void
    {
        $request = $this->requestFactory->customItemAction(
            'shop',
            Resources::ORDERS,
            $this->sharedStorage->get('cart_token'),
            HttpRequest::METHOD_PATCH,
            'product-bundle',
        );
        $request->updateContent([
            'productCode' => $product->getCode(),
            'quantity' => $quantity,
        ]);

        $this->client->executeCustomRequest($request);
    }

    /**
     * @When I add bundle :product with quantity :quantity to my cart and overwrite :oldVariant with :newVariant
     */
    public function iAddProductBundleToMyCartAndOverwriteVariant(
        ProductInterface $product,
        int $quantity,
        string $oldVariant,
        string $newVariant,
    ): void {
        $request = $this->requestFactory->customItemAction(
            'shop',
            Resources::ORDERS,
            $this->sharedStorage->get('cart_token'),
            HttpRequest::METHOD_PATCH,
            'product-bundle',
        );
        $request->updateContent([
            'productCode' => $product->getCode(),
            'quantity' => $quantity,
            'overwrittenVariants' => [
                [
                    AddProductBundleItemToCartCommandProvider::FROM => $oldVariant,
                    AddProductBundleItemToCartCommandProvider::TO => $newVariant,
                ],
            ],
        ]);

        $this->client->executeCustomRequest($request);
    }

    /**
     * @When I should have bundle :product with quantity :quantity in my cart
     */
    public function iShouldHaveBundleWithQuantityInMyCart(ProductInterface $product, int $quantity): void
    {
        $response = $this->client->show(Resources::ORDERS, $this->sharedStorage->get('cart_token'));

        $item = $this->responseChecker->getValue($response, 'items')[0];
        Assert::eq($item['productName'], $product->getName());
        Assert::eq($item['quantity'], $quantity);
    }

    /**
     * @When I should have product :product in bundled items
     */
    public function iShouldHaveProductInBundledItems(ProductInterface $product): void
    {
        $response = $this->client->show(Resources::ORDERS, $this->sharedStorage->get('cart_token'));

        $productBundleOrderItems = $this->responseChecker->getValue($response, 'items')[0]['productBundleOrderItems'];
        foreach ($productBundleOrderItems as $item) {
            if ($item['productVariant']['code'] === $product->getCode()) {
                return;
            }
        }

        throw new \InvalidArgumentException('Product not found in bundled items');
    }

    /**
     * @When I should have product variant :productVariant in bundled items
     */
    public function iShouldHaveProductVariantInBundledItems(ProductVariantInterface $productVariant): void
    {
        $response = $this->client->show(Resources::ORDERS, $this->sharedStorage->get('cart_token'));

        $productBundleOrderItems = $this->responseChecker->getValue($response, 'items')[0]['productBundleOrderItems'];
        foreach ($productBundleOrderItems as $item) {
            if ($item['productVariant']['code'] === $productVariant->getCode()) {
                return;
            }
        }

        throw new \InvalidArgumentException('Product not found in bundled items');
    }

    /**
     * @When I should not have product variant :productVariant in bundled items
     */
    public function iShouldNotHaveProductVariantInBundledItems(ProductVariantInterface $productVariant): void
    {
        $response = $this->client->show(Resources::ORDERS, $this->sharedStorage->get('cart_token'));

        $productBundleOrderItems = $this->responseChecker->getValue($response, 'items')[0]['productBundleOrderItems'];
        foreach ($productBundleOrderItems as $item) {
            if ($item['productVariant']['code'] === $productVariant->getCode()) {
                throw new \InvalidArgumentException(\sprintf('Product variant %s found in bundled items', $productVariant->getName()));
            }
        }
    }
}
