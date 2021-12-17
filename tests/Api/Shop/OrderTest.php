<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusProductBundlePlugin\Api\Shop;

use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\BitBag\SyliusProductBundlePlugin\Api\JsonApiTestCase;

final class OrderTest extends JsonApiTestCase
{
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
            self::DEFAULT_HEADER
        );
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/get_order_with_bundle_response', Response::HTTP_OK);
    }
}
