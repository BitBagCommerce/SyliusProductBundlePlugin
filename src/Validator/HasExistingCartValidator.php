<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Validator;

use BitBag\SyliusProductBundlePlugin\Command\OrderIdentityAwareInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Webmozart\Assert\Assert;

final class HasExistingCartValidator extends ConstraintValidator
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository
    ) {
    }

    /**
     * @param OrderIdentityAwareInterface|mixed $value
     * @param HasExistingCart|Constraint $constraint
     */
    public function validate($value, Constraint $constraint): void
    {
        Assert::isInstanceOf($constraint, HasExistingCart::class);

        if (!$value instanceof OrderIdentityAwareInterface) {
            throw new UnexpectedValueException($value, OrderIdentityAwareInterface::class);
        }

        $cart = $this->orderRepository->findCartById($value->getOrderId());

        if (null !== $cart) {
            return;
        }

        $this->context->addViolation(HasExistingCart::CART_DOESNT_EXIST_MESSAGE);
    }
}
