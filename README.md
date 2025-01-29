# [![](https://bitbag.io/wp-content/uploads/2021/04/ProductBundle.png)](https://bitbag.io/contact-us/?utm_source=github&utm_medium=referral&utm_campaign=plugins_productbundle)

# BitBag SyliusProductBundlePlugin

----

[ ![](https://img.shields.io/packagist/l/bitbag/product-bundle-plugin.svg) ](https://packagist.org/packages/bitbag/product-bundle-plugin "License")
[ ![](https://img.shields.io/packagist/v/bitbag/product-bundle-plugin.svg) ](https://packagist.org/packages/bitbag/product-bundle-plugin "Version")
[ ![](https://img.shields.io/github/actions/workflow/status/BitBagCommerce/SyliusProductBundlePlugin/build.yml?branch=master) ](https://github.com/BitBagCommerce/SyliusProductBundlePlugin/actions "Build status")
[ ![](https://poser.pugx.org/bitbag/product-bundle-plugin/downloads)](https://packagist.org/packages/bitbag/product-bundle-plugin "Total Downloads")
[ ![Slack](https://img.shields.io/badge/community%20chat-slack-FF1493.svg)](http://sylius.com/slack)
[ ![Support](https://img.shields.io/badge/support-contact%20author-blue])](https://bitbag.io/contact-us/?utm_source=github&utm_medium=referral&utm_campaign=plugins_productbundle)

We want to impact many unique eCommerce projects and build our brand recognition worldwide, so we are heavily involved in creating open-source solutions, especially for Sylius. We have already created over **35 extensions, which have been downloaded almost 2 million times.**

You can find more information about our eCommerce services and technologies on our website: https://bitbag.io/. We have also created a unique service dedicated to creating plugins: https://bitbag.io/services/sylius-plugin-development.

Do you like our work? Would you like to join us? Check out the **“Career” tab:** https://bitbag.io/pl/kariera.

# About Us
---

BitBag is a software house that implements tailor-made eCommerce platforms with the entire infrastructure—from creating eCommerce platforms to implementing PIM and CMS systems to developing custom eCommerce applications, specialist B2B solutions, and migrations from other platforms.

We actively participate in Sylius's development. We have already completed **over 150 projects**, cooperating with clients worldwide, including smaller enterprises and large international companies. We have completed projects for such important brands as **Mytheresa, Foodspring, Planeta Huerto (Carrefour Group), Albeco, Mollie, and ArtNight.**

We have a 70-person team of experts: business analysts and consultants, eCommerce developers, project managers, and QA testers.

**Our services:**
* B2B and B2C eCommerce platform implementations
* Multi-vendor marketplace platform implementations
* eCommerce migrations
* Sylius plugin development
* Sylius consulting
* Project maintenance and long-term support
* PIM and CMS implementations

**Some numbers from BitBag regarding Sylius:**
* 70 experts on board
* +150 projects delivered on top of Sylius
* 30 countries of BitBag’s customers
* 7 years in the Sylius ecosystem
* +35 plugins created for Sylius

---
[![](https://bitbag.io/wp-content/uploads/2024/09/badges-sylius.png)](https://bitbag.io/contact-us/?utm_source=github&utm_medium=referral&utm_campaign=plugins_productbandle)

---



## Table of Content

***

* [Overview](#overview)
* [Installation](#installation)
* [Testing](#testing)
* [Functionalities](#functionalities)
* [Demo](#demo)
* [License](#license)
* [Contact](#contact)
* [Community](#community)

# Overview

----
The **SyliusProductBundle** plugin allows you to create bundles from existing products in your store. Bundles appear and behave like any other product in your store. This will allow the customer to add multiple products to the cart simultaneously. What's more, products that are sold as bundles encourage customers to meet minimum shipping spending limits - increasing the average order value compared to free shipping.


# Installation
----
1. Require plugin with composer:

    ```bash
    composer require bitbag/product-bundle-plugin --no-scripts --with-all-dependencies
    ```

2. Add plugin dependencies to your `config/bundles.php` file after `Sylius\Bundle\ApiBundle\SyliusApiBundle`.

    ```php
        return [
         ...
        
            BitBag\SyliusProductBundlePlugin\BitBagSyliusProductBundlePlugin::class => ['all' => true ],
        ];
    ```

3. Import required config in your `config/packages/_sylius.yaml` file:

    ```yaml
    # config/packages/_sylius.yaml
    
    imports:
        ...
        
        - { resource: "@BitBagSyliusProductBundlePlugin/config/config.yml" }
    ```    

4. Import routing in your `config/routes.yaml` file:

    ```yaml
    
    # config/routes.yaml
    ...
    
    bitbag_sylius_product_bundle_plugin:
        resource: "@BitBagSyliusProductBundlePlugin/config/routes.yml"
    ```

5. Extend `Product`(including Doctrine mapping):

    ```php
    <?php 
   
    declare(strict_types=1);
    
    namespace App\Entity\Product;
    
    use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleInterface;
    use BitBag\SyliusProductBundlePlugin\Entity\ProductBundlesAwareTrait;
    use BitBag\SyliusProductBundlePlugin\Entity\ProductInterface;
    use Doctrine\ORM\Mapping as ORM;
    use Sylius\Component\Core\Model\Product as BaseProduct;
    use Sylius\Component\Product\Model\ProductTranslationInterface;

    #[ORM\Entity]
    #[ORM\Table(name: 'sylius_product')]
    class Product extends BaseProduct implements ProductInterface
    {
    use ProductBundlesAwareTrait;
    
        /**
         * @var ProductBundleInterface
         */
        #[ORM\OneToOne(
            targetEntity: "BitBag\SyliusProductBundlePlugin\Entity\ProductBundleInterface",
            mappedBy: "product",
            cascade: ["all"]
        )]
        protected $productBundle;
    
        protected function createTranslation(): ProductTranslationInterface
        {
            return new ProductTranslation();
        }
    }
    ```

   Mapping (XML):

   ```xml
   # config/doctrine/Product.Product.orm.xml
   
   <?xml version="1.0" encoding="UTF-8"?>
   <doctrine-mapping xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping"
                     xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                     xsi:schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping
                                         http://doctrine-project.org/schemas/orm/doctrine-mapping.xsd"
   >
       <entity name="App\Entity\Product\Product" table="sylius_product">
           <one-to-one field="productBundle" target-entity="BitBag\SyliusProductBundlePlugin\Entity\ProductBundleInterface" mapped-by="product">
               <cascade>
                   <cascade-all/>
               </cascade>
           </one-to-one>
       </entity>
   </doctrine-mapping>
   ```

7. Extend `OrderItem` (including Doctrine mapping):

    ```php
    <?php
   
    declare(strict_types=1);
   
    namespace App\Entity\Order;
   
    use BitBag\SyliusProductBundlePlugin\Entity\OrderItemInterface;
    use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleOrderItemInterface;
    use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleOrderItemsAwareTrait;
    use Doctrine\Common\Collections\ArrayCollection;
    use Doctrine\ORM\Mapping as ORM;
    use Sylius\Component\Core\Model\OrderItem as BaseOrderItem;
    
    #[ORM\Entity]
    #[ORM\Table(name: 'sylius_order_item')]
    class OrderItem extends BaseOrderItem implements OrderItemInterface
    {
    use ProductBundleOrderItemsAwareTrait;
    
        public function __construct()
        {
            parent::__construct();
            $this->init();
        }
    
        /**
         * @var ArrayCollection|ProductBundleOrderItemInterface[]
         */
        #[ORM\OneToMany(
            targetEntity: "BitBag\SyliusProductBundlePlugin\Entity\ProductBundleOrderItemInterface",
            mappedBy: "orderItem",
            cascade: ["all"]
        )]
        protected $productBundleOrderItems;
    }
    ```
   
   Mapping (XML):

   ```xml
   # config/doctrine/Order.OrderItem.orm.xml

   <?xml version="1.0" encoding="UTF-8"?>
   <doctrine-mapping xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping"
                     xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                     xsi:schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping
                                         http://doctrine-project.org/schemas/orm/doctrine-mapping.xsd"
   >
       <entity name="App\Entity\Order\OrderItem" table="sylius_order_item">
           <one-to-many field="productBundleOrderItems" target-entity="BitBag\SyliusProductBundlePlugin\Entity\ProductBundleOrderItem" mapped-by="orderItem" >
               <cascade>
                   <cascade-all/>
               </cascade>
           </one-to-many>
       </entity>
   </doctrine-mapping>
   ```

9. Add configuration for extended product, order item and product variant repository:

    ```yaml
    # config/packages/_sylius.yaml
   
    sylius_product:
        resources:
            product:
                classes:
                    model: App\Entity\Product\Product
            product_variant:
                classes:
                    model: App\Entity\Product\ProductVariant
                    repository: BitBag\SyliusProductBundlePlugin\Repository\ProductVariantRepository
   sylius_order:
       resources:
           order_item:
               classes:
                   model: App\Entity\Order\OrderItem
    
    ```

10. Add 'Create/Bundle' to product grid configuration:

    ```yaml
    # config/packages/_sylius.yaml
    
    sylius_grid:
        grids:
            sylius_admin_product:
                actions:
                    main:
                        create:
                            type: links
                            label: sylius.ui.create
                            options:
                                class: primary
                                icon: "tabler:plus"
                                header:
                                    icon: cube
                                    label: sylius.ui.type
                                links:
                                    simple:
                                        label: sylius.ui.simple_product
                                        route: sylius_admin_product_create_simple
                                    configurable:
                                        label: sylius.ui.configurable_product
                                        route: sylius_admin_product_create
                                    bundle:
                                        label: bitbag_sylius_product_bundle.ui.bundle
                                        route: bitbag_product_bundle_admin_product_create_bundle
       
    ```
11. If you have full configuration in xml override doctrine config :

    ```yaml
    # config/packages/doctrine.yaml   
    
    mappings:
            App:
                is_bundle: false
                type: xml
                dir: '%kernel.project_dir%/config/doctrine'
                prefix: 'App\Entity'
                alias: App
   
    
    ```

12. Copy plugin templates into your project `templates/bundles` directory:

    ```bash
    mkdir templates/bundles
    cp -R vendor/bitbag/product-bundle-plugin/tests/Application/templates/bundles/* templates/bundles/
    ```

13. Please clear application cache by running command below:

    ```bash
    bin/console cache:clear
    ```

14. Finish the installation by updating the database schema and installing assets:

    ```bash
    bin/console doctrine:migrations:diff
    bin/console doctrine:migrations:migrate
    ```
15. Add plugin assets to your project:
    [Import webpack config](./README_webpack-config.md)*

# Testing
----

```bash
composer install
cd tests/Application
yarn install
yarn build
bin/console assets:install public -e test
bin/console doctrine:schema:create -e test
bin/console server:run 127.0.0.1:8080 -d public -e test
open http://localhost:8080
vendor/bin/behat
```

# Functionalities

All main functionalities of the plugin are described **[here.](https://github.com/BitBagCommerce/SyliusProductBundlePlugin/blob/master/doc/functionalities.md)**

---

If you need some help with Sylius development, don't be hesitated to contact us directly. You can fill the form on [this site](https://bitbag.io/contact-us/?utm_source=github&utm_medium=referral&utm_campaign=plugins_productbundle) or send us an e-mail at hello@bitbag.io!

---
# Demo
---
We created a demo app with some useful use-cases of plugins! Visit http://demo.sylius.com/ to take a look at it.

**If you need an overview of Sylius' capabilities, schedule a consultation with our expert.**

[![](https://bitbag.io/wp-content/uploads/2020/10/button_free_consulatation-1.png)](https://bitbag.io/contact-us/?utm_source=github&utm_medium=referral&utm_campaign=plugins_productbundle)


# Additional resources for developers
---
To learn more about our contribution workflow and more, we encourage you to use the following resources:
* [Sylius Documentation](https://docs.sylius.com/en/latest/)
* [Sylius Contribution Guide](https://docs.sylius.com/en/latest/contributing/)
* [Sylius Online Course](https://sylius.com/online-course/)
* [Sylius Product Bundle Plugin Blog](https://bitbag.io/blog/product-bundling-sylius)

# License
---

This plugin's source code is completely free and released under the terms of the MIT license.

[//]: # (These are reference links used in the body of this note and get stripped out when the markdown processor does its job. There is no need to format nicely because it shouldn't be seen.)

# Contact
---
This open-source plugin was developed to help the Sylius community. If you have any additional questions, would like help with installing or configuring the plugin, or need any assistance with your Sylius project - let us know! **Contact us** or send us an **e-mail to hello@bitbag.io** with your question(s).

# Community
---- 

For online communication, we invite you to chat with us & other users on **[Sylius Slack](https://sylius-devs.slack.com/).**

[![](https://bitbag.io/wp-content/uploads/2024/09/badges-partners.png)](https://bitbag.io/contact-us/?utm_source=github&utm_medium=referral&utm_campaign=plugins_productbundle)
