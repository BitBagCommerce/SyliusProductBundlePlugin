<?php

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Form\Extension;

use BitBag\SyliusProductBundlePlugin\Entity\ProductInterface;
use BitBag\SyliusProductBundlePlugin\Form\Type\ProductBundleType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

final class ProductTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var ProductInterface $product */
        $product = $builder->getData();

        if (!$product->isBundle()) {
            return;
        }

        $builder
            ->add('productBundle', ProductBundleType::class, [
                'label' => false,
            ])
        ;
    }

    public function getExtendedType(): string
    {
        return ProductType::class;
    }
}
