<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusProductBundlePlugin\Api\Admin;

use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\BitBag\SyliusProductBundlePlugin\Api\AdminJsonApiTestCase;

final class ProductBundleTest extends AdminJsonApiTestCase
{
    private const JOHNNY_WALKER_BUNDLE_PRODUCT_IRI = '/api/v2/admin/products/JOHNNY_WALKER_BUNDLE';
    private const ENDPOINT_PRODUCT_BUNDLES_COLLECTION = '/api/v2/admin/product-bundles';
    private const ENDPOINT_PRODUCT_BUNDLES_ITEM = '/api/v2/admin/product-bundles/%d';

    /** @var object[]  */
    private $fixtures = [];
    /** @var string[]  */
    private $authHeaders = [];

    protected function setUp(): void
    {
        $this->fixtures = $this->loadFixturesFromFiles(['general/channels.yml', 'general/authentication.yml', 'shop/product_bundles.yml']);
        $authToken = $this->getAuthToken('api@example.com', 'sylius');
        $this->authHeaders = $this->getHeaders($authToken);
    }

    /** @test */
    public function it_gets_product_bundles_collection(): void
    {
        $this->client->request(
            Request::METHOD_GET,
            self::ENDPOINT_PRODUCT_BUNDLES_COLLECTION,
            [],
            [],
            $this->authHeaders
        );
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'admin/get_product_bundle_collection_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_gets_product_bundle(): void
    {
        /** @var ProductBundleInterface $productBundleId */
        $productBundleId = $this->fixtures['productBundle1'];

        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::ENDPOINT_PRODUCT_BUNDLES_ITEM, $productBundleId->getId()),
            [],
            [],
            $this->authHeaders
        );
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'admin/get_product_bundle_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_creates_product_bundle(): void
    {
        $johnnyWalkerBlack = $this->createProductBundleItemObject('JOHNNY_WALKER_BLACK');
        $johnnyWalkerBlue = $this->createProductBundleItemObject('JOHNNY_WALKER_BLUE');

        $this->client->request(
            Request::METHOD_POST,
            self::ENDPOINT_PRODUCT_BUNDLES_COLLECTION,
            [],
            [],
            $this->authHeaders,
            json_encode([
                'product' => self::JOHNNY_WALKER_BUNDLE_PRODUCT_IRI,
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
        /** @var ProductBundleInterface $productBundle */
        $productBundle = $this->fixtures['productBundle1'];

        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::ENDPOINT_PRODUCT_BUNDLES_ITEM, $productBundle->getId()),
            [],
            [],
            $this->authHeaders
        );
        /** @var string $baseResponseContent */
        $baseResponseContent = $this->client->getResponse()->getContent();
        $baseProductBundle = json_decode($baseResponseContent, true, 512, JSON_THROW_ON_ERROR);
        $baseBundleItems = $baseProductBundle['items'] ?? [];

        $johnnyWalkerBlue = $this->createProductBundleItemObject('JOHNNY_WALKER_BLUE');
        $johnnyWalkerGold = $this->createProductBundleItemObject('JOHNNY_WALKER_GOLD');

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::ENDPOINT_PRODUCT_BUNDLES_ITEM, $productBundle->getId()),
            [],
            [],
            $this->authHeaders,
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
        $updatedProductBundle = json_decode($updateResponseContent, true, 512, JSON_THROW_ON_ERROR);
        $updatedBundleItems = $updatedProductBundle['items'] ?? [];

        foreach ($updatedBundleItems as $bundleItem) {
            $this->assertNotContains($bundleItem, $baseBundleItems);
        }
    }

    private function createProductBundleItemObject(string $productVariantCode, int $quantity = 1): object
    {
        $productBundleItem = new \stdClass();
        $productBundleItem->productVariant = '/api/v2/admin/product-variants/' . $productVariantCode;
        $productBundleItem->quantity = $quantity;

        return $productBundleItem;
    }

    /** @test */
    public function it_deletes_product_bundle(): void
    {
        /** @var ProductBundleInterface $productBundle */
        $productBundle = $this->fixtures['productBundle1'];

        $this->client->request(
            Request::METHOD_DELETE,
            sprintf(self::ENDPOINT_PRODUCT_BUNDLES_ITEM, $productBundle->getId()),
            [],
            [],
            $this->authHeaders
        );
        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::ENDPOINT_PRODUCT_BUNDLES_ITEM, $productBundle->getId()),
            [],
            [],
            $this->authHeaders
        );
        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_NOT_FOUND);
    }
}
