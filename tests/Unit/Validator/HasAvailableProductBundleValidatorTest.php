<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusProductBundlePlugin\Unit\Validator;

use BitBag\SyliusProductBundlePlugin\Command\AddProductBundleToCartCommand;
use BitBag\SyliusProductBundlePlugin\Entity\ProductInterface;
use BitBag\SyliusProductBundlePlugin\Validator\HasAvailableProductBundle;
use BitBag\SyliusProductBundlePlugin\Validator\HasAvailableProductBundleValidator;
use PHPUnit\Framework\MockObject\MockObject;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;
use Tests\BitBag\SyliusProductBundlePlugin\Unit\MotherObject\ChannelMother;
use Tests\BitBag\SyliusProductBundlePlugin\Unit\MotherObject\OrderMother;
use Tests\BitBag\SyliusProductBundlePlugin\Unit\MotherObject\ProductMother;
use Tests\BitBag\SyliusProductBundlePlugin\Unit\MotherObject\ProductVariantMother;

final class HasAvailableProductBundleValidatorTest extends ConstraintValidatorTestCase
{
    private const ORDER_ID = 5;

    private const PRODUCT_CODE = 'MY_PROD';

    private const CHANNEL_NAME = 'My Awesome Channel';

    /** @var mixed|MockObject|ProductRepositoryInterface */
    private $productRepository;

    /** @var mixed|MockObject|OrderRepositoryInterface */
    private $orderRepository;

    /** @var mixed|MockObject|AvailabilityCheckerInterface */
    private $availabilityChecker;

    protected function setUp(): void
    {
        $this->productRepository = $this->createMock(ProductRepositoryInterface::class);
        $this->orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $this->availabilityChecker = $this->createMock(AvailabilityCheckerInterface::class);

        parent::setUp();
    }

    /**
     * @dataProvider pessimisticDataProvider
     */
    public function testPessimisticCase(
        ProductInterface $product,
        ?OrderInterface $cart,
        bool $isStockSufficient,
        string $violationMessage,
        array $violationParameters
    ): void {
        $this->productRepository->method('findOneByCode')
            ->with(self::PRODUCT_CODE)
            ->willReturn($product)
        ;

        $this->orderRepository->method('findCartById')
            ->with(self::ORDER_ID)
            ->willReturn($cart)
        ;

        $productVariant = $product->getVariants()->first();
        $this->availabilityChecker->method('isStockSufficient')
            ->with($productVariant, 1)
            ->willReturn($isStockSufficient)
        ;

        $command = new AddProductBundleToCartCommand(self::ORDER_ID, self::PRODUCT_CODE);
        $constraint = new HasAvailableProductBundle();

        $this->validator->validate($command, $constraint);

        $this->buildViolation($violationMessage)
            ->setParameters($violationParameters)
            ->assertRaised();
    }

    public function pessimisticDataProvider(): iterable
    {
        yield 'product is disabled' => $this->getProductDisabledCaseData();
        yield 'product variant is disabled' => $this->getProductVariantDisabledCaseData();
        yield 'product\'s channel and cart\'s channel are different' => $this->getProductAndCartChannelsAreDifferentCaseData();
        yield 'product\'s quantity in the cart exceeds the stock' => $this->getProductQuantityExceedsStockCaseData();
    }

    private function getProductDisabledCaseData(): array
    {
        $product = ProductMother::createDisabledWithCode(self::PRODUCT_CODE);
        $violationMessage = HasAvailableProductBundle::PRODUCT_DISABLED_MESSAGE;
        $violationParameters = [
            '{{ code }}' => self::PRODUCT_CODE,
        ];

        return [$product, null, false, $violationMessage, $violationParameters];
    }

    private function getProductVariantDisabledCaseData(): array
    {
        $productVariant = ProductVariantMother::createDisabledWithCode(self::PRODUCT_CODE);
        $product = ProductMother::createWithProductVariantAndCode($productVariant, self::PRODUCT_CODE);
        $violationMessage = HasAvailableProductBundle::PRODUCT_VARIANT_DISABLED_MESSAGE;
        $violationParameters = [
            '{{ code }}' => self::PRODUCT_CODE,
        ];

        return [$product, null, false, $violationMessage, $violationParameters];
    }

    private function getProductAndCartChannelsAreDifferentCaseData(): array
    {
        $productVariant = ProductVariantMother::createWithCode(self::PRODUCT_CODE);
        $product = ProductMother::createWithProductVariantAndCode($productVariant, self::PRODUCT_CODE);

        $channel = ChannelMother::createWithName(self::CHANNEL_NAME);
        $cart = OrderMother::createWithChannel($channel);

        $violationMessage = HasAvailableProductBundle::PRODUCT_DOESNT_EXIST_IN_CHANNEL_MESSAGE;
        $violationParameters = [
            '{{ channel }}' => self::CHANNEL_NAME,
            '{{ code }}' => self::PRODUCT_CODE,
        ];

        return [$product, $cart, false, $violationMessage, $violationParameters];
    }

    private function getProductQuantityExceedsStockCaseData(): array
    {
        $channel = ChannelMother::createWithName(self::CHANNEL_NAME);
        $productVariant = ProductVariantMother::createWithCode(self::PRODUCT_CODE);
        $product = ProductMother::createWithChannelAndProductVariantAndCode(
            $channel,
            $productVariant,
            self::PRODUCT_CODE
        );

        $cart = OrderMother::createWithChannel($channel);

        $violationMessage = HasAvailableProductBundle::PRODUCT_VARIANT_INSUFFICIENT_STOCK_MESSAGE;
        $violationParameters = [
            '{{ code }}' => self::PRODUCT_CODE,
        ];

        return [$product, $cart, false, $violationMessage, $violationParameters];
    }

    protected function createValidator(): HasAvailableProductBundleValidator
    {
        return new HasAvailableProductBundleValidator(
            $this->productRepository,
            $this->orderRepository,
            $this->availabilityChecker
        );
    }
}
