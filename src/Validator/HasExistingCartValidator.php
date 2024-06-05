<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
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
        private OrderRepositoryInterface $orderRepository,
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
