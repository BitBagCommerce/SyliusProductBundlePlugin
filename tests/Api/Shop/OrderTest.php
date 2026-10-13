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

namespace Tests\Sylius\ProductBundlePlugin\Api\Shop;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\ProductBundlePlugin\Entity\OrderItemInterface;
use Sylius\ProductBundlePlugin\Entity\ProductBundleInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\Sylius\ProductBundlePlugin\Api\JsonApiTestCase;
use Tests\Sylius\ProductBundlePlugin\Api\Utils\CartHelperTrait;

final class OrderTest extends JsonApiTestCase
{
    use CartHelperTrait;

    private const CART_TOKEN = 'DszffX9DZIx';

    private const ENDPOINT_ORDERS_PRODUCT_BUNDLE = '/api/v2/shop/orders/%s/product-bundle';

    private const ENDPOINT_ORDERS_ITEM = '/api/v2/shop/orders/%s';

    /** @var array|object[] */
    private $fixtures = [];

    protected function setUp(): void
    {
        $this->fixtures = $this->loadFixturesFromFiles([
            'general/channels.yml',
            'shop/product_bundles.yml',
            'shop/orders.yml',
        ]);
    }

    /** @test */
    public function it_gets_order_data_containing_info_about_bundled_items(): void
    {
        /** @var OrderInterface $order */
        $order = $this->fixtures['order_with_bundle'];

        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::ENDPOINT_ORDERS_ITEM, $order->getTokenValue()),
            [],
            [],
            self::DEFAULT_HEADER,
        );

        $response = $this->client->getResponse();

        $this->assertResponseWithVersionSupport($response, 'shop/get_order_with_bundle_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_adds_product_bundle_to_a_cart(): void
    {
        $this->createCart(self::CART_TOKEN);
        /** @var ProductBundleInterface $productBundle */
        $productBundle = $this->fixtures['productBundle1'];

        $this->client->request(
            Request::METHOD_PATCH,
            sprintf(self::ENDPOINT_ORDERS_PRODUCT_BUNDLE, self::CART_TOKEN),
            [],
            [],
            self::PATCH_HEADER,
            json_encode([
                'productCode' => $productBundle->getProduct()->getCode(),
            ], \JSON_THROW_ON_ERROR),
        );
        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_OK);

        $cart = $this->findCart(self::CART_TOKEN);
        /** @var OrderItemInterface $cartItem */
        $cartItem = $cart->getItems()->first();
        $cartProduct = $cartItem->getProduct();

        self::assertCount(1, $cart->getItems());
        self::assertCount(2, $cartItem->getProductBundleOrderItems());
        self::assertSame($productBundle->getProduct()->getId(), $cartProduct->getId());
    }
}
