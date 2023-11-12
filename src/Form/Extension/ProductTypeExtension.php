<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Form\Extension;

use BitBag\SyliusProductBundlePlugin\Form\Type\ProductBundleType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Valid;

final class ProductTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('productBundle', ProductBundleType::class, [
                'label' => false,
                'constraints' => [new Valid()],
            ])
        ;
    }

    public static function getExtendedTypes(): iterable
    {
        return [ProductType::class];
    }
}
