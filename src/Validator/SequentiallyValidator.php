<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class SequentiallyValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof Sequentially) {
            throw new UnexpectedTypeException($constraint, Sequentially::class);
        }

        $context = $this->context;

        $validator = $context->getValidator()->inContext($context);

        $originalCount = $validator->getViolations()->count();

        if (is_iterable($constraint->constraints)) {
            foreach ($constraint->constraints as $c) {
                if ($originalCount !== $validator->validate($value, $c)->getViolations()->count()) {
                    break;
                }
            }
        }
    }
}
