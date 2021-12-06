<?php

declare(strict_types=1);

namespace Tests\BitBag\SyliusProductBundlePlugin\Api\Admin;

use ApiTestCase\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class ProductBundleTest extends JsonApiTestCase
{
    public const CONTENT_TYPE_HEADER = ['CONTENT_TYPE' => 'application/ld+json', 'HTTP_ACCEPT' => 'application/ld+json'];
    private $fixtures = [];

    protected function setUp(): void
    {
        $this->fixtures = $this->loadFixturesFromFiles(['general/channels.yml', 'shop/product_bundles.yml']);
    }

    /** @test */
    public function it_gets_product_bundles_collection(): void
    {
        $this->client->request(
            'GET',
            '/api/v2/admin/product-bundles',
            [],
            [],
            self::CONTENT_TYPE_HEADER
        );
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'admin/get_product_bundle_collection_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_gets_product_bundle(): void
    {
        $productBundleId = $this->fixtures['productBundle1']->getId();

        $this->client->request(
            'GET',
            '/api/v2/admin/product-bundles/' . $productBundleId,
            [],
            [],
            self::CONTENT_TYPE_HEADER
        );
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'admin/get_product_bundle_response', Response::HTTP_OK);
    }
}
