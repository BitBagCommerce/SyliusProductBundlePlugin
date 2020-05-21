<h1 align="center">
    <a href="http://bitbag.shop" target="_blank">
        <img src="doc/logo.jpeg" width="55%" />
    </a>
    <br />
    <a href="https://packagist.org/packages/bitbag/product-bundle-plugin" title="License" target="_blank">
        <img src="https://img.shields.io/packagist/l/bitbag/product-bundle-plugin.svg" />
    </a>
    <a href="https://packagist.org/packages/bitbag/product-bundle-plugin" title="Total Downloads" target="_blank">
        <img src="https://poser.pugx.org/bitbag/product-bundle-plugin/downloads" />
    </a>

</h1>

## About us

At BitBag we do believe in open source. However, we are able to do it just beacuse of our awesome clients, who are kind enough to share some parts of our work with the community. Therefore, if you feel like there is a possibility for us working together, feel free to reach us out. You will find out more about our professional services, technologies and contact details at https://bitbag.io/.

## Overview

This plugin allows you to create new products by bundling existing products together.

## Installation

1. Require plugin with composer:

    ```bash
    composer require bitbag/product-bundle-plugin
    ```
 
1. Add plugin dependencies to your `config/bundles.php` file:
    
    ```php
        return [
         ...
        
            BitBag\SyliusProductBundlePlugin\BitBagSyliusProductBundlePlugin::class => ['all' => true ],
        ];
    ```

1. Import required config in your `config/packages/_sylius.yaml` file:
    
    ```yaml
    # config/packages/_sylius.yaml
    
    imports:
        ...
        
        - { resource: "@BitBagSyliusProductBundlePlugin/Resources/config/config.yml" }
    ```    

1. Import routing in your `config/routes.yaml` file:
    
    ```yaml
    
    # config/routes.yaml
    ...
    
    bitbag_sylius_product_bundle_plugin:
        resource: "@BitBagSyliusProductBundlePlugin/Resources/config/routing.yml"
    ```

1. Extend `Product`(including Doctrine mapping)

    ```php
    <?php
    
    declare(strict_types=1);
    
    namespace App\Entity;
    
    use BitBag\SyliusProductBundlePlugin\Entity\ProductBundlesAwareInterface;
    use BitBag\SyliusProductBundlePlugin\Entity\ProductBundlesAwareTrait;
    use Doctrine\ORM\Mapping as ORM;
    use Sylius\Component\Core\Model\Product as BaseProduct;
    
    /**
     * @ORM\Entity
     * @ORM\Table(name="sylius_product")
     */
    class Product extends BaseProduct implements ProductBundlesAwareInterface
    {
        use ProductBundlesAwareTrait;
    
        /**
         * @OneToOne(targetEntity="BitBag\SyliusProductBundlePlugin\Entity\ProductBundle", mappedBy="product", cascade={"persist", "remove"})
         * @var ProductBundleInterface 
         **/
        protected $productBundle;
    
    
    ```

1. Add configuration for extended product and product variant repository:

    ```yaml
    # config/packages/_sylius.yaml
    sylius_product:
        resources:
            product:
                classes:
                    model: App\Entity\Product\Product
            product_variant:
                classes:
                    repository: BitBag\SyliusProductBundlePlugin\Repository\ProductVariantRepository
    
    ```

1. Add 'Create/Bundle' to product grid configuration:

    ```yaml
    # config/packages/_sylius.yaml
    
    sylius_grid:
        grids:
            sylius_admin_product:
                actions:
                    main:
                        create:
                            links:
                                bundle:
                                    label: bitbag_sylius_product_bundle.ui.bundle
                                    icon: plus
                                    route: bitbag_product_bundle_admin_product_create_bundle
    
    ```
    
1. Finish the installation by updating the database schema and installing assets:

    ```
    $ bin/console doctrine:migrations:diff
    $ bin/console doctrine:migrations:migrate
    ```

## Testing
```bash
$ composer install
$ cd tests/Application
$ yarn install
$ yarn run gulp
$ bin/console assets:install public -e test
$ bin/console doctrine:schema:create -e test
$ bin/console server:run 127.0.0.1:8080 -d public -e test
$ open http://localhost:8080
$ vendor/bin/behat
```

## Contribution

Learn more about our contribution workflow on http://docs.sylius.org/en/latest/contributing/.
