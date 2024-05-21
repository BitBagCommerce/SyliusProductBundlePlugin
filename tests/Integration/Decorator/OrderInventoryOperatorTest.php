<?php

declare(strict_types=1);

namespace Tests\BitBag\SyliusProductBundlePlugin\Integration\Decorator;

use BitBag\SyliusProductBundlePlugin\Doctrine\ORM\Inventory\Operator\OrderInventoryOperator;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Product\Repository\ProductVariantRepositoryInterface;
use Tests\BitBag\SyliusProductBundlePlugin\Api\JsonApiTestCase;

final class OrderInventoryOperatorTest extends JsonApiTestCase
{
    private EntityManagerInterface $entityManager;
    private ProductVariantRepositoryInterface $repository;
    private OrderInventoryOperator $orderInventoryOperator;

    /** @var array|object[] */
    private $fixtures = [];

    protected function setUp(): void
    {
        $this->entityManager = $this->getContainer()->get('doctrine.orm.entity_manager');
        $this->repository = $this->getContainer()->get('sylius.repository.product_variant');
        $this->orderInventoryOperator = $this->getContainer()->get('bitbag_sylius_product_bundle.inventory.order_inventory_operator');
    }

    public function testDoesNotUpdateBundledProductsStockOnSell(): void
    {
        // given
        $_ENV['APP_UPDATE_BUNDLED_PRODUCTS_STOCK'] = 0;

        $this->orderInventoryOperator = $this->getContainer()->get('bitbag_sylius_product_bundle.inventory.order_inventory_operator');
        $this->fixtures = $this->loadFixturesFromFile('test.yml');
        $order = $this->entityManager->getRepository(Order::class)->findOneBy(['number' => $this->fixtures['order_product_bundle']->getNumber()]);

        // when
        $stateMachine = $this->getContainer()->get('')
        $this->orderInventoryOperator->sell($order);

        // then
        /** @var ProductVariantInterface $productBundleProductVariant */
        $productVariantRepository = $this->entityManager->
        $productBundleProductVariant = $this->repository->findOneBy(['code' => $this->fixtures['product_variant_product_bundle_variant']->getCode()]);
        $firstBundledProductProductVariant = $this->repository->findOneBy(['code' => $this->fixtures['product_variant_first_bundled_product_variant']->getCode()]);
        $secondBundledProductVariant = $this->repository->findOneBy(['code' => $this->fixtures['product_variant_second_bundled_product_variant']->getCode()]);
        self::assertSame(9, $productBundleProductVariant->getOnHand());
        self::assertSame(10, $firstBundledProductProductVariant->getOnHand());
        self::assertSame(10, $secondBundledProductVariant->getOnHand());
    }
}