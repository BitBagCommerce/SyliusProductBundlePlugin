# 
   <h1 align="center">
       <a href="http://bitbag.shop" target="_blank">
           <img src="https://bitbag.io/wp-content/uploads/2021/04/ProductBundle.png" alt="BitBag" />
       </a>
   </h1>

# BitBag SyliusProductBundlePlugin

----

[![](https://img.shields.io/packagist/l/bitbag/product-bundle-plugin.svg) ](https://packagist.org/packages/bitbag/product-bundle-plugin "License") [ ![](https://img.shields.io/packagist/v/bitbag/product-bundle-plugin.svg) ](https://packagist.org/packages/bitbag/product-bundle-plugin "Version") [ ![](https://img.shields.io/github/workflow/status/BitBagCommerce/SyliusProductBundlePlugin/Build) ](https://github.com/BitBagCommerce/SyliusProductBundlePlugin/actions "Build status") [![](https://poser.pugx.org/bitbag/product-bundle-plugin/downloads)](https://packagist.org/packages/bitbag/product-bundle-plugin "Total Downloads") [![Slack](https://img.shields.io/badge/community%20chat-slack-FF1493.svg)](http://sylius-devs.slack.com) [![Support](https://img.shields.io/badge/support-contact%20author-blue])](https://bitbag.io/contact-us/?utm_source=github&utm_medium=referral&utm_campaign=plugins_productbundle)

At BitBag we do believe in open source. However, we are able to do it just because of our awesome clients, who are kind enough to share some parts of our work with the community. Therefore, if you feel like there is a possibility for us working together, feel free to reach us out. You will find out more about our professional services, technologies and contact details at [https://bitbag.io/](https://bitbag.io/contact-us/?utm_source=github&utm_medium=referral&utm_campaign=plugins_productbundle).

## Table of Content

***

* [Overview](#overwiev)
* [Support](#we-are-here-to-help)
* [Installation](#installation)
   * [Testing](#testing)
* [About us](#about-us)
   * [Community](#community)
* [Demo Sylius shop](#demo-sylius-shop)
* [Additional Sylius resources for developers](#additional-resources-for-developers)
* [License](#license)
* [Contact](#contact)

# Overview

----
This plugin allows you to create new products by bundling existing products together.


## We are here to help
This **open-source plugin was developed to help the Sylius community** and make Mollie payments platform available to any Sylius store. If you have any additional questions, would like help with installing or configuring the plugin or need any assistance with your Sylius project - let us know!

[![](https://bitbag.io/wp-content/uploads/2020/10/button-contact.png)](https://bitbag.io/contact-us/?utm_source=github&utm_medium=referral&utm_campaign=plugins_productbundle)


# Installation

----
1. Require plugin with composer:

    ```bash
    composer require bitbag/product-bundle-plugin
    ```

2. Add plugin dependencies to your `config/bundles.php` file:

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
        
        - { resource: "@BitBagSyliusProductBundlePlugin/Resources/config/config.yml" }
    ```    

4. Import routing in your `config/routes.yaml` file:

    ```yaml
    
    # config/routes.yaml
    ...
    
    bitbag_sylius_product_bundle_plugin:
        resource: "@BitBagSyliusProductBundlePlugin/Resources/config/routing.yml"
    ```

5. Extend `Product`(including Doctrine mapping):

    ```php
    <?php 
   
   declare(strict_types=1);
    
    namespace App\Entity\Product;
    
    use BitBag\SyliusProductBundlePlugin\Entity\ProductBundlesAwareInterface;
    use BitBag\SyliusProductBundlePlugin\Entity\ProductBundlesAwareTrait;
    use Sylius\Component\Core\Model\Product as BaseProduct;

    class Product extends BaseProduct implements ProductBundlesAwareInterface
    {
        use ProductBundlesAwareTrait;  
    }
    ```

   Mapping (Annotations) - Override bundle trait, by create new one and use it in Entity/Product/Product .

   ```php
   use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleInterface;
   use BitBag\SyliusProductBundlePlugin\Entity\ProductBundlesAwareTrait;
   use Doctrine\ORM\Mapping as ORM;
   
   trait ProductTrait
   {
   use ProductBundlesAwareTrait;
   
       /**
        * @var ProductBundleInterface
        * @ORM\OneToOne(
        *     targetEntity="BitBag\SyliusProductBundlePlugin\Entity\ProductBundleInterface",
        *     mappedBy="product",
        *     cascade={"all"},)
        */
       protected $productBundle;
   
   }
   ```

   Mapping (XML):

   ```xml
   # Resources/config/doctrine/Product.Product.orm.xml
   
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

6. Extend `OrderItem` (including Doctrine mapping):

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
   Mapping (Annotations) - Override bundle trait, by create new one and use it in Entity/Order/OrderItem .

   ```php
   use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleOrderItemInterface;
   use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleOrderItemsAwareTrait;
   use Doctrine\Common\Collections\ArrayCollection;
   use Doctrine\ORM\Mapping as ORM;
   
   trait OrderItemTrait
   {
   use ProductBundleOrderItemsAwareTrait;
   
       /**
        * @var ArrayCollection|ProductBundleOrderItemInterface[]
        * @ORM\OneToMany(
        *     targetEntity="BitBag\SyliusProductBundlePlugin\Entity\ProductBundleOrderItemInterface",
        *     mappedBy="orderItem",
        *     cascade={"all"},)
        */
       protected $productBundleOrderItems;
   
   }
   ```
   Mapping (XML):

   ```xml
   # Resources/config/doctrine/Order.OrderItem.orm.xml

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

7. Add configuration for extended product, order item and product variant repository:

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
   sylius_order:
       resources:
           order_item:
               classes:
                   model: App\Entity\Order\OrderItem
    
    ```

8. Add 'Create/Bundle' to product grid configuration:

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
                               icon: plus
                               header:
                                   icon: cube
                                   label: sylius.ui.type
                               links:
                                   simple:
                                       label: sylius.ui.simple_product
                                       icon: plus
                                       route: sylius_admin_product_create_simple
                                   configurable:
                                       label: sylius.ui.configurable_product
                                       icon: plus
                                       route: sylius_admin_product_create
                                   bundle:
                                       label: bitbag_sylius_product_bundle.ui.bundle
                                       icon: plus
                                       route: bitbag_product_bundle_admin_product_create_bundle
       
    ```
9. If you have full configuration in xml override doctrine config :

    ```yaml
    # config/packages/doctrine.yaml   
    
    mappings:
            App:
                is_bundle: false
                type: xml
                dir: '%kernel.project_dir%/src/Resources/config/doctrine'
                prefix: 'App\Entity'
                alias: App
   
    
    ``` 

10. Finish the installation by updating the database schema and installing assets:

    ```
    $ bin/console doctrine:migrations:diff
    $ bin/console doctrine:migrations:migrate
    ```

## Testing

----

```bash
$ composer install
$ cd tests/Application
$ yarn install
$ yarn build
$ bin/console assets:install public -e test
$ bin/console doctrine:schema:create -e test
$ bin/console server:run 127.0.0.1:8080 -d public -e test
$ open http://localhost:8080
$ vendor/bin/behat
```



# About us

---

BitBag is an agency that provides high-quality **eCommerce and Digital Experience software**. Our main area of expertise includes eCommerce consulting and development for B2C, B2B, and Multi-vendor Marketplaces.
The scope of our services related to Sylius includes:
- **Consulting** in the field of strategy development
- Personalized **headless software development**
- **System maintenance and long-term support**
- **Outsourcing**
- **Plugin development**
- **Data migration**

Some numbers regarding Sylius:
* **20+ experts** including consultants, UI/UX designers, Sylius trained front-end and back-end developers,
* **100+ projects** delivered on top of Sylius,
* Clients from  **20+ countries**
* **3+ years** in the Sylius ecosystem.

---

If you need some help with Sylius development, don't be hesitate to contact us directly. You can fill the form on [this site](https://bitbag.io/contact-us/?utm_source=github&utm_medium=referral&utm_campaign=plugins_productbundle) or send us an e-mail to hello@bitbag.io!

---

[![](https://bitbag.io/wp-content/uploads/2020/10/badges-sylius.png)](https://bitbag.io/contact-us/?utm_source=github&utm_medium=referral&utm_campaign=plugins_productbundle)

## Community

----
For online communication, we invite you to chat with us & other users on [Sylius Slack](https://sylius-devs.slack.com/).

# Demo Sylius shop

---

We created a demo app with some useful use-cases of plugins!
Visit b2b.bitbag.shop to take a look at it. The admin can be accessed under b2b.bitbag.shop/admin/login link and sylius: sylius credentials.
Plugins that we have used in the demo:

| BitBag's Plugin | GitHub | Sylius' Store|
| ------ | ------ | ------|
| ACL Plugin | *Private. Available after the purchasing.*| https://plugins.sylius.com/plugin/access-control-layer-plugin/|
| Braintree Plugin | https://github.com/BitBagCommerce/SyliusBraintreePlugin |https://plugins.sylius.com/plugin/braintree-plugin/|
| CMS Plugin | https://github.com/BitBagCommerce/SyliusCmsPlugin | https://plugins.sylius.com/plugin/cmsplugin/|
| Elasticsearch Plugin | https://github.com/BitBagCommerce/SyliusElasticsearchPlugin | https://plugins.sylius.com/plugin/2004/|
| Mailchimp Plugin | https://github.com/BitBagCommerce/SyliusMailChimpPlugin | https://plugins.sylius.com/plugin/mailchimp/ |
| Multisafepay Plugin | https://github.com/BitBagCommerce/SyliusMultiSafepayPlugin |
| Wishlist Plugin | https://github.com/BitBagCommerce/SyliusWishlistPlugin | https://plugins.sylius.com/plugin/wishlist-plugin/|
| **Sylius' Plugin** | **GitHub** | **Sylius' Store** |
| Admin Order Creation Plugin | https://github.com/Sylius/AdminOrderCreationPlugin | https://plugins.sylius.com/plugin/admin-order-creation-plugin/ |
| Invoicing Plugin | https://github.com/Sylius/InvoicingPlugin | https://plugins.sylius.com/plugin/invoicing-plugin/ |
| Refund Plugin | https://github.com/Sylius/RefundPlugin | https://plugins.sylius.com/plugin/refund-plugin/ |

**If you need an overview of Sylius' capabilities, schedule a consultation with our expert.**

[![](https://bitbag.io/wp-content/uploads/2020/10/button_free_consulatation-1.png)](https://bitbag.io/contact-us/?utm_source=github&utm_medium=referral&utm_campaign=plugins_productbundle)

## Additional resources for developers

---
To learn more about our contribution workflow and more, we encourage ypu to use the following resources:
* [Sylius Documentation](https://docs.sylius.com/en/latest/)
* [Sylius Contribution Guide](https://docs.sylius.com/en/latest/contributing/)
* [Sylius Online Course](https://sylius.com/online-course/)

## License

---

This plugin's source code is completely free and released under the terms of the MIT license.

[//]: # (These are reference links used in the body of this note and get stripped out when the markdown processor does its job. There is no need to format nicely because it shouldn't be seen.)

## Contact

---
If you want to contact us, the best way is to fill the form on [our website](https://bitbag.io/contact-us/?utm_source=github&utm_medium=referral&utm_campaign=plugins_productbundle) or send us an e-mail to hello@bitbag.io with your question(s). We guarantee that we answer as soon as we can!

[![](https://bitbag.io/wp-content/uploads/2020/10/footer.png)](https://bitbag.io/contact-us/?utm_source=github&utm_medium=referral&utm_campaign=plugins_productbundle)
