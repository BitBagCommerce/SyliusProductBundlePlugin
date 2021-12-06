<?php

declare(strict_types=1);

namespace Tests\BitBag\SyliusProductBundlePlugin\Api\Shop;

use ApiTestCase\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

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