<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Validator;

use BitBag\SyliusProductBundlePlugin\Command\OrderIdentifierAwareInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class HasValidOrderIdentifierValidator extends ConstraintValidator
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param OrderIdentifierAwareInterface|mixed $value
     * @param HasValidOrderIdentifier|Constraint $constraint
     */
    public function validate($value, Constraint $constraint): void
    {
        Assert::isInstanceOf($value, OrderIdentifierAwareInterface::class);
        Assert::isInstanceOf($constraint, HasValidOrderIdentifier::class);

        if (null === $value->getOrderId() && null === $value->getOrderToken()) {
            $this->context->addViolation(HasValidOrderIdentifier::NO_ORDER_ID_OR_TOKEN_MESSAGE);

            return;
        }

        $cart = $this->findCart($value);

        if (null === $cart) {
            $this->context->addViolation(HasValidOrderIdentifier::CART_DOESNT_EXIST);

            return;
        }
    }

    private function findCart(OrderIdentifierAwareInterface $value): ?OrderInterface
    {
        if (null !== $value->getOrderId()) {
            return $this->orderRepository->findCartById($value->getOrderId());
        }

        return $this->orderRepository->findCartByTokenValue($value->getOrderToken());
    }
}
