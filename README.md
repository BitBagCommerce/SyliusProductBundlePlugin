# [![](https://bitbag.io/wp-content/uploads/2021/04/ProductBundle.png)](https://bitbag.io/contact-us/?utm_source=github&utm_medium=referral&utm_campaign=plugins_productbundle)

# BitBag SyliusProductBundlePlugin

----

[ ![](https://img.shields.io/packagist/l/bitbag/product-bundle-plugin.svg) ](https://packagist.org/packages/bitbag/product-bundle-plugin "License") 
[ ![](https://img.shields.io/packagist/v/bitbag/product-bundle-plugin.svg) ](https://packagist.org/packages/bitbag/product-bundle-plugin "Version") 
[ ![](https://img.shields.io/github/actions/workflow/status/BitBagCommerce/SyliusProductBundlePlugin/build.yml?branch=master) ](https://github.com/BitBagCommerce/SyliusProductBundlePlugin/actions "Build status") 
[ ![](https://poser.pugx.org/bitbag/product-bundle-plugin/downloads)](https://packagist.org/packages/bitbag/product-bundle-plugin "Total Downloads") 
[ ![Slack](https://img.shields.io/badge/community%20chat-slack-FF1493.svg)](http://sylius-devs.slack.com) 
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
    composer require bitbag/product-bundle-plugin --no-scripts
    ```

2. (optional) Add plugin dependencies to your `config/bundles.php` file after `Sylius\Bundle\ApiBundle\SyliusApiBundle`.

    ```php
        return [
         ...
        
            BitBag\SyliusProductBundlePlugin\BitBagSyliusProductBundlePlugin::class => ['all' => true ],
        ];
    ```

3. (optional) Import required config in your `config/packages/_sylius.yaml` file:

    ```yaml
    # config/packages/_sylius.yaml
    
    imports:
      ...
        
      - { resource: "@BitBagSyliusProductBundlePlugin/Resources/config/config.yml" }
    ```    

4. (optional) Import routing in your `config/routes.yaml` file:

    ```yaml
    
    # config/routes.yaml
    ...
    
    bitbag_sylius_product_bundle_plugin:
      resource: "@BitBagSyliusProductBundlePlugin/Resources/config/routing.yml"
    ```

5. (applied if using Rector) Extend entities by running
    ```bash
    vendor/bin/rector process src --config=vendor/bitbag/product-bundle-plugin/rector.php 
    ```

6. (applied if not using Rector) Extend `Product` (including Doctrine mapping):

    ```php
    <?php 
   
    declare(strict_types=1);
    
    namespace App\Entity\Product;
    
    use BitBag\SyliusProductBundlePlugin\Entity\ProductBundlesAwareTrait;
    use BitBag\SyliusProductBundlePlugin\Entity\ProductInterface;
    use Sylius\Component\Core\Model\Product as BaseProduct;

    class Product extends BaseProduct implements ProductInterface
    {
        use ProductBundlesAwareTrait;  
    }
    ```

7. (applied if using XML for mapping) Add mapping for Product (Resources/config/doctrine/Product.Product.orm.xml):

   ```xml 
   <?xml version="1.0" encoding="UTF-8"?>
   <doctrine-mapping xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping"
                     xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                     xsi:schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping
                                         http://doctrine-project.org/schemas/orm/doctrine-mapping.xsd"
   >
       <entity name="App\Entity\Product\Product" table="sylius_product">
           <one-to-one field="productBundle" target-entity="BitBag\SyliusProductBundlePlugin\Entity\ProductBundleInterface" 
                       mapped-by="product" orphan-removal="true">
                <cascade>
                    <cascade-all/>
                </cascade>
           </one-to-one>
       </entity>
   </doctrine-mapping>
   ```

8. (applied if not using Rector) Extend `OrderItem` (including Doctrine mapping):

    ```php
   <?php
   
   declare(strict_types=1);
   
   namespace App\Entity\Order;
   
   use BitBag\SyliusProductBundlePlugin\Entity\OrderItemInterface;
   use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleOrderItemsAwareTrait;
   use Sylius\Component\Core\Model\OrderItem as BaseOrderItem;
   
   class OrderItem extends BaseOrderItem implements OrderItemInterface
   {
       use ProductBundleOrderItemsAwareTrait;
   
       public function __construct()
       {
           parent::__construct();
           $this->init();
       }
   
   }
    ```
   
9. (applied if using XML for mapping) Add mapping for OrderItem (Resources/config/doctrine/Order.OrderItem.orm.xml):
   
   ```xml
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

10. (optional) If you want to manage stock for bundled products instead of managing bundle stock, add this to your .env:

    ```dotenv
    ###> bitbag/product-bundle-plugin ###
    BUNDLED_PRODUCTS_INVENTORY_MANAGEMENT_FEATURE=true
    ###< bitbag/product-bundle-plugin ###
    ```

11. Add configuration for extended product and order item:

    ```yaml
    # config/packages/_sylius.yaml
   
    sylius_product:
      resources:
        product:
          classes:
            model: App\Entity\Product\Product

    sylius_order:
      resources:
        order_item:
          classes:
            model: App\Entity\Order\OrderItem
    
    ```

12. If you have full configuration in xml override doctrine config:

```yaml
# config/packages/doctrine.yaml   
doctrine:
  orm:
    entity_managers:
    default:
      mappings:
        App:
          is_bundle: false
          type: xml
          dir: '%kernel.project_dir%/src/Resources/config/doctrine'
          prefix: 'App\Entity'
          alias: App

```

13. Add plugin templates:
- Inject blocks:

```yaml
sylius_ui:
  events:
    sylius.shop.product.show.right_sidebar:
      blocks:
        variant_selection:
          template: "@BitBagSyliusProductBundlePlugin/Shop/Product/_variantSelection.html.twig"
          priority: 10

    sylius.shop.layout.javascripts:
      blocks:
        plugin_scripts:
          template: "@BitBagSyliusProductBundlePlugin/Shop/_scripts.html.twig"
          priority: 20

    sylius.shop.layout.stylesheets:
      blocks:
        plugin_stylesheets:
          template: "@BitBagSyliusProductBundlePlugin/Shop/_styles.html.twig"
          priority: 20

    sylius.admin.layout.javascripts:
      blocks:
        plugin_scripts:
          template: "@BitBagSyliusProductBundlePlugin/Admin/_scripts.html.twig"
          priority: 20

    sylius.admin.layout.stylesheets:
      blocks:
        plugin_stylesheets:
          template: "@BitBagSyliusProductBundlePlugin/Admin/_styles.html.twig"
          priority: 20

```
- Copy plugin templates into your project `templates/bundles` directory:

    ```bash
    cp -R vendor/bitbag/product-bundle-plugin/src/Resources/views/Admin/Order templates/bundles/SyliusAdminBundle
    cp -R vendor/bitbag/product-bundle-plugin/src/Resources/views/Admin/Product templates/bundles/SyliusAdminBundle
    cp -R vendor/bitbag/product-bundle-plugin/src/Resources/views/Shop/Cart templates/bundles/SyliusShopBundle
    cp -R vendor/bitbag/product-bundle-plugin/src/Resources/views/Shop/Common templates/bundles/SyliusShopBundle
    ```
    
14. Please clear application cache by running command below:

    ```bash
    bin/console cache:clear
    ```

15. Finish the installation by updating the database schema and installing assets:

    ```bash
    bin/console doctrine:migrations:migrate
    ```
16. Add plugin assets to your project:
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
