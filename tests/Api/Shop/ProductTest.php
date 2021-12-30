<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusProductBundlePlugin\Api\Shop;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\BitBag\SyliusProductBundlePlugin\Api\JsonApiTestCase;

final class ProductTest extends JsonApiTestCase
{
    private const ENDPOINT_PRODUCTS_ITEM = '/api/v2/shop/products/%s';

    private const ENDPOINT_PRODUCTS_ITEM_PRODUCT_BUNDLE = '/api/v2/shop/products/%s/bundle';

    protected function setUp(): void
    {
        $this->loadFixturesFromFiles(['general/channels.yml', 'shop/product_bundles.yml']);
    }

    /** @test */
    public function it_gets_bundled_product(): void
    {
        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::ENDPOINT_PRODUCTS_ITEM, 'WHISKEY_DOUBLE_PACK'),
            [],
            [],
            self::DEFAULT_HEADER
        );
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/get_bundled_product_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_gets_not_bundled_product(): void
    {
        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::ENDPOINT_PRODUCTS_ITEM, 'JOHNNY_WALKER_BLACK'),
            [],
            [],
            self::DEFAULT_HEADER
        );
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/get_not_bundled_product_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_gets_product_bundle_as_a_subresource(): void
    {
        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::ENDPOINT_PRODUCTS_ITEM_PRODUCT_BUNDLE, 'WHISKEY_DOUBLE_PACK'),
            [],
            [],
            self::DEFAULT_HEADER
        );
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/get_product_bundle_response', Response::HTTP_OK);
    }
}
