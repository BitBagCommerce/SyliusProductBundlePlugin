<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusProductBundlePlugin\Api\Admin;

use Symfony\Component\HttpFoundation\Response;
use Tests\BitBag\SyliusProductBundlePlugin\Api\AdminJsonApiTestCase;

final class ProductBundleTest extends AdminJsonApiTestCase
{
    private $fixtures = [];
    private $headers = [];

    protected function setUp(): void
    {
        $this->fixtures = $this->loadFixturesFromFiles(['general/channels.yml', 'general/authentication.yml', 'shop/product_bundles.yml']);
        $authToken = $this->getAuthToken('api@example.com', 'sylius');
        $this->headers = $this->getHeaders($authToken);
    }

    /** @test */
    public function it_gets_product_bundles_collection(): void
    {
        $this->client->request(
            'GET',
            '/api/v2/admin/product-bundles',
            [],
            [],
            $this->headers
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
            $this->headers
        );
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'admin/get_product_bundle_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_creates_product_bundle(): void
    {
        $johnnyWalkerBlack = new \stdClass();
        $johnnyWalkerBlack->productVariant = '/api/v2/admin/product-variants/JOHNNY_WALKER_BLACK';
        $johnnyWalkerBlack->quantity = 1;
        $johnnyWalkerBlue = new \stdClass();
        $johnnyWalkerBlue->productVariant = '/api/v2/admin/product-variants/JOHNNY_WALKER_BLUE';
        $johnnyWalkerBlue->quantity = 1;

        $this->client->request(
            'POST',
            '/api/v2/admin/product-bundles',
            [],
            [],
            $this->headers,
            json_encode([
                'product' => '/api/v2/admin/products/JOHNNY_WALKER_BUNDLE',
                'items' => [
                    $johnnyWalkerBlack,
                    $johnnyWalkerBlue,
                ],
                'isPacked' => true,
            ], JSON_THROW_ON_ERROR)
        );
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'admin/post_product_bundle_response', Response::HTTP_CREATED);
    }

    /** @test */
    public function it_updates_product_bundle(): void
    {
        $productBundleId = $this->fixtures['productBundle1']->getId();

        $this->client->request(
            'GET',
            '/api/v2/admin/product-bundles/' . $productBundleId,
            [],
            [],
            $this->headers
        );
        /** @var string $baseResponseContent */
        $baseResponseContent = $this->client->getResponse()->getContent();
        $baseProductBundle = json_decode($baseResponseContent, true);
        $baseBundleItems = $baseProductBundle['items'] ?? [];

        $johnnyWalkerBlue = $this->createProductBundleItemObject('JOHNNY_WALKER_BLUE', 1);
        $johnnyWalkerGold = $this->createProductBundleItemObject('JOHNNY_WALKER_GOLD', 1);

        $this->client->request(
            'PUT',
            '/api/v2/admin/product-bundles/' . $productBundleId,
            [],
            [],
            $this->headers,
            json_encode([
                'items' => [
                    $johnnyWalkerBlue,
                    $johnnyWalkerGold,
                ],
                'isPacked' => false,
            ], JSON_THROW_ON_ERROR)
        );
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'admin/put_product_bundle_response', Response::HTTP_OK);

        /** @var string $updateResponseContent */
        $updateResponseContent = $response->getContent();
        $updatedProductBundle = json_decode($updateResponseContent, true);
        $updatedBundleItems = $updatedProductBundle['items'] ?? [];

        foreach ($updatedBundleItems as $bundleItem) {
            $this->assertNotContains($bundleItem, $baseBundleItems);
        }
    }

    private function createProductBundleItemObject(string $productVariantCode, int $quantity): object
    {
        $productBundleItem = new \stdClass();
        $productBundleItem->productVariant = '/api/v2/admin/product-variants/' . $productVariantCode;
        $productBundleItem->quantity = $quantity;

        return $productBundleItem;
    }
}
