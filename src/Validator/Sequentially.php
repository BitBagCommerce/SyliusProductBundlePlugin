<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Validator;

use Symfony\Component\Validator\Constraints\Composite;

final class Sequentially extends Composite
{
    /** @var array */
    public $constraints = [];

    public function __construct(?array $constraints = null)
    {
        parent::__construct($constraints ?? []);
    }

    public function getDefaultOption(): string
    {
        return 'constraints';
    }

    public function getRequiredOptions(): array
    {
        return ['constraints'];
    }

    protected function getCompositeOption(): string
    {
        return 'constraints';
    }

    /**
     * @return array<string>|string
     */
    public function getTargets()
    {
        return [self::CLASS_CONSTRAINT, self::PROPERTY_CONSTRAINT];
    }

    public function validatedBy(): string
    {
        return 'bitbag_sylius_product_bundle_validator_sequentially';
    }
}
