<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusProductBundlePlugin\Api\Shop;

use BitBag\SyliusProductBundlePlugin\Entity\OrderItemInterface;
use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\BitBag\SyliusProductBundlePlugin\Api\JsonApiTestCase;
use Tests\BitBag\SyliusProductBundlePlugin\Api\Utils\CartHelperTrait;

final class ProductBundleTest extends JsonApiTestCase
{
    use CartHelperTrait;

    private const CART_TOKEN = 'zszRdAaZIx';
    private const ENDPOINT_PRODUCT_BUNDLE_ADD_TO_CART = '/api/v2/shop/product-bundles/%d/add-to-cart';

    /** @var object[]  */
    private $fixtures;

    protected function setUp(): void
    {
        $this->fixtures = $this->loadFixturesFromFiles([
            'general/channels.yml',
            'shop/product_bundles.yml',
        ]);
    }

    /** @test */
    public function it_adds_product_bundle_to_a_cart(): void
    {
        $this->createCart(self::CART_TOKEN);
        /** @var ProductBundleInterface $productBundle */
        $productBundle = $this->fixtures['productBundle1'];

        $this->client->request(
            Request::METHOD_PATCH,
            sprintf(self::ENDPOINT_PRODUCT_BUNDLE_ADD_TO_CART, $productBundle->getId()),
            [],
            [],
            self::PATCH_HEADER,
            json_encode([
                'orderToken' => self::CART_TOKEN,
            ], JSON_THROW_ON_ERROR)
        );
        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_OK);

        $cart = $this->findCart(self::CART_TOKEN);
        /** @var OrderItemInterface $cartItem */
        $cartItem = $cart->getItems()->first();
        $cartProduct = $cartItem->getProduct();

        $this->assertCount(1, $cart->getItems());
        $this->assertCount(2, $cartItem->getProductBundleOrderItems());
        $this->assertSame($productBundle->getProduct()->getId(), $cartProduct->getId());
    }
}
