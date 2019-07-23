<?php

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceAutocompleteChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

final class ProductBundleItemType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantity', IntegerType::class, [
                'label' => false,
                'required' => false,
            ])
            ->add('productVariant', ResourceAutocompleteChoiceType::class, [
                'label' => false,
                'choice_name' => 'descriptor',
                'choice_value' => 'code',
                'resource' => 'sylius.product_variant',
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'bitbag_sylius_product_bundle_plugin_product_bundle_item';
    }
}
