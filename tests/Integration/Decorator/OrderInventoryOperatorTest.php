<?php

declare(strict_types=1);

namespace Tests\BitBag\SyliusProductBundlePlugin\Integration\Decorator;

use SM\Factory\FactoryInterface;
use Sylius\Component\Core\OrderPaymentTransitions;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\OrderTransitions;
use Sylius\Component\Product\Repository\ProductVariantRepositoryInterface;
use Tests\BitBag\SyliusProductBundlePlugin\Api\JsonApiTestCase;

final class OrderInventoryOperatorTest extends JsonApiTestCase
{
    private ProductVariantRepositoryInterface $productVariantRepository;

    private OrderRepositoryInterface $orderRepository;

    private FactoryInterface $stateMachineFactory;

    /** @var array|object[] */
    private $fixtures = [];

    public function OrderPaymentStateFixturesProvider(): array
    {
        return [
            [
                'fixture' => 'order_payment_state_paid.yml',
                'expectedProductBundleOnHand' => 11,
                'expectedProductBundleOnHold' => 1,
            ],
            [
                'fixture' => 'order_payment_state_refunded.yml',
                'expectedProductBundleOnHand' => 11,
                'expectedProductBundleOnHold' => 1,
            ],
            [
                'fixture' => 'order_payment_state_not_paid_not_refunded.yml',
                'expectedProductBundleOnHand' => 10,
                'expectedProductBundleOnHold' => 0,
            ]
        ];
    }

    public function onOrderCancellationConditionsProvider(): array
    {
        return [
            [
                'envValue' => 0,
                'fixture' => 'order_payment_state_paid.yml',
                'expectedProductBundleOnHand' => 11,
                'expectedProductBundleOnHold' => 1,
                'expectedFirstBundledProductOnHand' => 10,
                'expectedFirstBundledProductOnHold' => 1,
                'expectedSecondBundledProductOnHand' => 10,
                'expectedSecondBundledProductOnHold' => 2,
            ],
            [
                'envValue' => 0,
                'fixture' => 'order_payment_state_refunded.yml',
                'expectedProductBundleOnHand' => 11,
                'expectedProductBundleOnHold' => 1,
                'expectedFirstBundledProductOnHand' => 10,
                'expectedFirstBundledProductOnHold' => 1,
                'expectedSecondBundledProductOnHand' => 10,
                'expectedSecondBundledProductOnHold' => 2,
            ],
            [
                'envValue' => 0,
                'fixture' => 'order_payment_state_not_paid_not_refunded.yml',
                'expectedProductBundleOnHand' => 10,
                'expectedProductBundleOnHold' => 0,
                'expectedFirstBundledProductOnHand' => 10,
                'expectedFirstBundledProductOnHold' => 1,
                'expectedSecondBundledProductOnHand' => 10,
                'expectedSecondBundledProductOnHold' => 2,
            ],
            [
                'envValue' => 1,
                'fixture' => 'order_payment_state_paid.yml',
                'expectedProductBundleOnHand' => 11,
                'expectedProductBundleOnHold' => 1,
                'expectedFirstBundledProductOnHand' => 11,
                'expectedFirstBundledProductOnHold' => 1,
                'expectedSecondBundledProductOnHand' => 12,
                'expectedSecondBundledProductOnHold' => 2,
            ],
            [
                'envValue' => 1,
                'fixture' => 'order_payment_state_refunded.yml',
                'expectedProductBundleOnHand' => 11,
                'expectedProductBundleOnHold' => 1,
                'expectedFirstBundledProductOnHand' => 11,
                'expectedFirstBundledProductOnHold' => 1,
                'expectedSecondBundledProductOnHand' => 12,
                'expectedSecondBundledProductOnHold' => 2,
            ],
            [
                'envValue' => 1,
                'fixture' => 'order_payment_state_not_paid_not_refunded.yml',
                'expectedProductBundleOnHand' => 10,
                'expectedProductBundleOnHold' => 0,
                'expectedFirstBundledProductOnHand' => 10,
                'expectedFirstBundledProductOnHold' => 0,
                'expectedSecondBundledProductOnHand' => 10,
                'expectedSecondBundledProductOnHold' => 0,
            ],
        ];
    }

    public function onOrderCreationConditionsProvider(): array
    {
        return [
            [
                'envValue' => 0,
                'expectedProductBundleOnHand' => 10,
                'expectedProductBundleOnHold' => 2,
                'expectedFirstBundledProductOnHand' => 10,
                'expectedFirstBundledProductOnHold' => 1,
                'expectedSecondBundledProductOnHand' => 10,
                'expectedSecondBundledProductOnHold' => 2,
            ],
            [
                'envValue' => 1,
                'expectedProductBundleOnHand' => 10,
                'expectedProductBundleOnHold' => 2,
                'expectedFirstBundledProductOnHand' => 10,
                'expectedFirstBundledProductOnHold' => 2,
                'expectedSecondBundledProductOnHand' => 10,
                'expectedSecondBundledProductOnHold' => 4,
            ],
        ];
    }

    public function onOrderPayConditionsProvider(): array
    {
        return [
            [
                'envValue' => 0,
                'expectedProductBundleOnHand' => 9,
                'expectedProductBundleOnHold' => 0,
                'expectedFirstBundledProductOnHand' => 10,
                'expectedFirstBundledProductOnHold' => 1,
                'expectedSecondBundledProductOnHand' => 10,
                'expectedSecondBundledProductOnHold' => 2,
            ],
            [
                'envValue' => 1,
                'expectedProductBundleOnHand' => 9,
                'expectedProductBundleOnHold' => 0,
                'expectedFirstBundledProductOnHand' => 9,
                'expectedFirstBundledProductOnHold' => 0,
                'expectedSecondBundledProductOnHand' => 8,
                'expectedSecondBundledProductOnHold' => 0,
            ],
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->productVariantRepository = $this->getContainer()->get('sylius.repository.product_variant');
        $this->orderRepository = $this->getContainer()->get('sylius.repository.order');
        $this->stateMachineFactory = $this->getContainer()->get(FactoryInterface::class);
    }

    /** @dataProvider OrderPaymentStateFixturesProvider */
    public function testDoesNotUpdateBundledProductsStockOnCancelIfEnvVariableIsFalse(
        string $orderPaymentStateFixture,
        int $expectedProductBundleOnHand,
        int $expectedProductBundleOnHold,
    ): void {
        // given
        $_ENV['APP_UPDATE_BUNDLED_PRODUCTS_STOCK'] = 0;
        $this->fixtures = $this->loadFixturesFromFiles([
            'bundled_products_stock_update.yml',
            $orderPaymentStateFixture
        ]);
        $order = $this->orderRepository->find($this->fixtures['order_product_bundle']->getId());
        $stateMachine = $this->stateMachineFactory->get($order, OrderTransitions::GRAPH);

        // when
        $stateMachine->apply(OrderTransitions::TRANSITION_CANCEL);

        // then
        $productBundleProductVariant = $this->productVariantRepository->findOneBy(
            ['id' => $this->fixtures['product_variant_product_bundle_variant']->getId()]
        );
        $firstBundledProductProductVariant = $this->productVariantRepository->findOneBy(
            ['id' => $this->fixtures['product_variant_first_bundled_product_variant']->getId()]
        );
        $secondBundledProductVariant = $this->productVariantRepository->findOneBy(
            ['id' => $this->fixtures['product_variant_second_bundled_product_variant']->getId()]
        );
        self::assertSame($expectedProductBundleOnHand, $productBundleProductVariant->getOnHand());
        self::assertSame($expectedProductBundleOnHold, $productBundleProductVariant->getOnHold());
        self::assertSame(
            $this->fixtures['product_variant_first_bundled_product_variant']->getOnHand(),
            $firstBundledProductProductVariant->getOnHand()
        );
        self::assertSame(
            $this->fixtures['product_variant_first_bundled_product_variant']->getOnHold(),
            $firstBundledProductProductVariant->getOnHold()
        );
        self::assertSame(
            $this->fixtures['product_variant_second_bundled_product_variant']->getOnHand(),
            $secondBundledProductVariant->getOnHand()
        );
        self::assertSame(
            $this->fixtures['product_variant_second_bundled_product_variant']->getOnHold(),
            $secondBundledProductVariant->getOnHold()
        );
    }

    /** @dataProvider onOrderCancellationConditionsProvider */
    public function testStockUpdateOnOrderCancellation(
        int $envValue,
        string $orderPaymentStateFixture,
        int $expectedProductBundleOnHand,
        int $expectedProductBundleOnHold,
        int $expectedFirstBundledProductOnHand,
        int $expectedFirstBundledProductOnHold,
        int $expectedSecondBundledProductOnHand,
        int $expectedSecondBundledProductOnHold,
    ): void {
        // given
        $_ENV['APP_UPDATE_BUNDLED_PRODUCTS_STOCK'] = $envValue;
        $this->fixtures = $this->loadFixturesFromFiles([
            'bundled_products_stock_update.yml',
            $orderPaymentStateFixture
        ]);
        $order = $this->orderRepository->find($this->fixtures['order_product_bundle']->getId());
        $stateMachine = $this->stateMachineFactory->get($order, OrderTransitions::GRAPH);

        // when
        $stateMachine->apply(OrderTransitions::TRANSITION_CANCEL);

        // then
        $productBundleProductVariant = $this->productVariantRepository->findOneBy(
            ['id' => $this->fixtures['product_variant_product_bundle_variant']->getId()]
        );
        $firstBundledProductProductVariant = $this->productVariantRepository->findOneBy(
            ['id' => $this->fixtures['product_variant_first_bundled_product_variant']->getId()]
        );
        $secondBundledProductVariant = $this->productVariantRepository->findOneBy(
            ['id' => $this->fixtures['product_variant_second_bundled_product_variant']->getId()]
        );
        self::assertSame($expectedProductBundleOnHand, $productBundleProductVariant->getOnHand());
        self::assertSame($expectedProductBundleOnHold, $productBundleProductVariant->getOnHold());
        self::assertSame($expectedFirstBundledProductOnHand, $firstBundledProductProductVariant->getOnHand());
        self::assertSame($expectedFirstBundledProductOnHold, $firstBundledProductProductVariant->getOnHold());
        self::assertSame($expectedSecondBundledProductOnHand, $secondBundledProductVariant->getOnHand());
        self::assertSame($expectedSecondBundledProductOnHold, $secondBundledProductVariant->getOnHold());
    }

    /** @dataProvider onOrderCreationConditionsProvider */
    public function testStockUpdateOnOrderCreation(
        int $envValue,
        int $expectedProductBundleOnHand,
        int $expectedProductBundleOnHold,
        int $expectedFirstBundledProductOnHand,
        int $expectedFirstBundledProductOnHold,
        int $expectedSecondBundledProductOnHand,
        int $expectedSecondBundledProductOnHold,
    ): void {
        // given
        $_ENV['APP_UPDATE_BUNDLED_PRODUCTS_STOCK'] = $envValue;
        $this->fixtures = $this->loadFixturesFromFiles([
            'bundled_products_stock_update.yml',
            'order_state_cart.yml'
        ]);
        $order = $this->orderRepository->find($this->fixtures['order_product_bundle']->getId());
        $stateMachine = $this->stateMachineFactory->get($order, OrderTransitions::GRAPH);

        // when
        $stateMachine->apply(OrderTransitions::TRANSITION_CREATE);

        // then
        $productBundleProductVariant = $this->productVariantRepository->findOneBy(
            ['id' => $this->fixtures['product_variant_product_bundle_variant']->getId()]
        );
        $firstBundledProductProductVariant = $this->productVariantRepository->findOneBy(
            ['id' => $this->fixtures['product_variant_first_bundled_product_variant']->getId()]
        );
        $secondBundledProductVariant = $this->productVariantRepository->findOneBy(
            ['id' => $this->fixtures['product_variant_second_bundled_product_variant']->getId()]
        );
        self::assertSame($expectedProductBundleOnHand, $productBundleProductVariant->getOnHand());
        self::assertSame($expectedProductBundleOnHold, $productBundleProductVariant->getOnHold());
        self::assertSame($expectedFirstBundledProductOnHand, $firstBundledProductProductVariant->getOnHand());
        self::assertSame($expectedFirstBundledProductOnHold, $firstBundledProductProductVariant->getOnHold());
        self::assertSame($expectedSecondBundledProductOnHand, $secondBundledProductVariant->getOnHand());
        self::assertSame($expectedSecondBundledProductOnHold, $secondBundledProductVariant->getOnHold());
    }

    /** @dataProvider onOrderPayConditionsProvider */
    public function testUpdatesBundledProductsStockOnSell(
        int $envValue,
        $expectedProductBundleOnHand,
        $expectedProductBundleOnHold,
        $expectedFirstBundledProductOnHand,
        $expectedFirstBundledProductOnHold,
        $expectedSecondBundledProductOnHand,
        $expectedSecondBundledProductOnHold,
    ): void
    {
        // given
        $_ENV['APP_UPDATE_BUNDLED_PRODUCTS_STOCK'] = $envValue;
        $this->fixtures = $this->loadFixturesFromFiles([
            'bundled_products_stock_update.yml',
            'order_payment_state_not_paid_not_refunded.yml'
        ]);
        $order = $this->orderRepository->find($this->fixtures['order_product_bundle']->getId());
        $stateMachine = $this->stateMachineFactory->get($order, OrderPaymentTransitions::GRAPH);

        // when
        $stateMachine->apply(OrderPaymentTransitions::TRANSITION_PAY);

        // then
        $productBundleProductVariant = $this->productVariantRepository->findOneBy(
            ['id' => $this->fixtures['product_variant_product_bundle_variant']->getId()]
        );
        $firstBundledProductProductVariant = $this->productVariantRepository->findOneBy(
            ['id' => $this->fixtures['product_variant_first_bundled_product_variant']->getId()]
        );
        $secondBundledProductVariant = $this->productVariantRepository->findOneBy(
            ['id' => $this->fixtures['product_variant_second_bundled_product_variant']->getId()]
        );

        self::assertSame($expectedProductBundleOnHand, $productBundleProductVariant->getOnHand());
        self::assertSame($expectedProductBundleOnHold, $productBundleProductVariant->getOnHold());
        self::assertSame($expectedFirstBundledProductOnHand, $firstBundledProductProductVariant->getOnHand());
        self::assertSame($expectedFirstBundledProductOnHold, $firstBundledProductProductVariant->getOnHold());
        self::assertSame($expectedSecondBundledProductOnHand, $secondBundledProductVariant->getOnHand());
        self::assertSame($expectedSecondBundledProductOnHold, $secondBundledProductVariant->getOnHold());
    }
}