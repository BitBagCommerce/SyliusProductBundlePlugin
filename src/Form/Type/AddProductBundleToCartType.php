<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Form\Type;

use BitBag\SyliusProductBundlePlugin\Dto\AddProductBundleToCartDto;
use BitBag\SyliusProductBundlePlugin\Entity\ProductInterface;
use Sylius\Bundle\OrderBundle\Form\Type\CartItemType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AddProductBundleToCartType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('cartItem', CartItemType::class, [
                'product' => $options['product'],
            ])
            ->add('productBundleItems', CollectionType::class, [
                'entry_type' => AddProductBundleItemToCartType::class,
                'entry_options' => [
                    'product' => $options['product'],
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefined([
                'product',
            ])
            ->setAllowedTypes('product', ProductInterface::class)
            ->setDefaults([
                'data_class' => AddProductBundleToCartDto::class,
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'bitbag_sylius_product_bundle_plugin_add_product_bundle_to_cart';
    }
}
