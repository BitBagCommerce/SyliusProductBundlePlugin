<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusProductBundlePlugin\Api\Shop;

use Symfony\Component\HttpFoundation\Response;
use Tests\BitBag\SyliusProductBundlePlugin\Api\JsonApiTestCase;

final class ProductTest extends JsonApiTestCase
{
    public const CONTENT_TYPE_HEADER = ['CONTENT_TYPE' => 'application/ld+json', 'HTTP_ACCEPT' => 'application/ld+json'];

    protected function setUp(): void
    {
        $this->loadFixturesFromFiles(['general/channels.yml', 'shop/product_bundles.yml']);
    }

    /** @test */
    public function it_gets_bundled_product(): void
    {
        $this->client->request(
            'GET',
            '/api/v2/shop/products/WHISKEY_DOUBLE_PACK',
            [],
            [],
            self::CONTENT_TYPE_HEADER
        );
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/get_bundled_product_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_gets_not_bundled_product(): void
    {
        $this->client->request(
            'GET',
            '/api/v2/shop/products/JOHNNY_WALKER_BLACK',
            [],
            [],
            self::CONTENT_TYPE_HEADER
        );
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/get_not_bundled_product_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_gets_product_bundle_as_a_subresource(): void
    {
        $this->client->request(
            'GET',
            '/api/v2/shop/products/WHISKEY_DOUBLE_PACK/bundle',
            [],
            [],
            self::CONTENT_TYPE_HEADER
        );
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/get_product_bundle_response', Response::HTTP_OK);
    }
}
