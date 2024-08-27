# [![](https://bitbag.io/wp-content/uploads/2021/04/ProductBundle.png)](https://bitbag.io/contact-us/?utm_source=github&utm_medium=referral&utm_campaign=plugins_productbundle)

# BitBag SyliusProductBundlePlugin

----

[ ![](https://img.shields.io/packagist/l/bitbag/product-bundle-plugin.svg) ](https://packagist.org/packages/bitbag/product-bundle-plugin "License") 
[ ![](https://img.shields.io/packagist/v/bitbag/product-bundle-plugin.svg) ](https://packagist.org/packages/bitbag/product-bundle-plugin "Version") 
[ ![](https://img.shields.io/github/actions/workflow/status/BitBagCommerce/SyliusProductBundlePlugin/build.yml?branch=master) ](https://github.com/BitBagCommerce/SyliusProductBundlePlugin/actions "Build status") 
[ ![](https://poser.pugx.org/bitbag/product-bundle-plugin/downloads)](https://packagist.org/packages/bitbag/product-bundle-plugin "Total Downloads") 
[ ![Slack](https://img.shields.io/badge/community%20chat-slack-FF1493.svg)](http://sylius-devs.slack.com) 
[ ![Support](https://img.shields.io/badge/support-contact%20author-blue])](https://bitbag.io/contact-us/?utm_source=github&utm_medium=referral&utm_campaign=plugins_productbundle)

At BitBag we do believe in open source. However, we are able to do it just because of our awesome clients, who are kind enough to share some parts of our work with the community. Therefore, if you feel like there is a possibility for us to work  together, feel free to reach out. You will find out more about our professional services, technologies, and contact details at [https://bitbag.io/](https://bitbag.io/contact-us/?utm_source=github&utm_medium=referral&utm_campaign=plugins_productbundle).

Like what we do? Want to join us? Check out our job listings on our [career page](https://bitbag.io/career/?utm_source=github&utm_medium=referral&utm_campaign=career). Not familiar with Symfony & Sylius yet, but still want to start with us? Join our [academy](https://bitbag.io/pl/akademia?utm_source=github&utm_medium=url&utm_campaign=akademia)!

## Table of Content

***

* [Overview](#overview)
* [Support](#we-are-here-to-help)
* [Installation](#installation)
   * [Testing](#testing)
* [About us](#about-us)
   * [Community](#community)
* [Demo](#demo-sylius-shop)
* [License](#license)
* [Contact](#contact)

# Overview

----
This plugin allows you to create new products by bundling existing products together.


## We are here to help
This **open-source plugin was developed to help the Sylius community**. If you have any additional questions, would like help with installing or configuring the plugin, or need any assistance with your Sylius project - let us know!

[![](https://bitbag.io/wp-content/uploads/2020/10/button-contact.png)](https://bitbag.io/contact-us/?utm_source=github&utm_medium=referral&utm_campaign=plugins_productbundle)

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

5. Extend `Product`(including Doctrine mapping):

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

   Mapping (Attributes) - Override bundle trait, by create new one and use it in Entity/Product/Product .
   
   **Note.** If you're using Attributes Mapping, please use your `ProductTrait` in your `Product` entity instead of plugins `ProductBundlesAwareTrait`.

   ```php
   <?php 
   
   declare(strict_types=1);
   
   use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleInterface;
   use BitBag\SyliusProductBundlePlugin\Entity\ProductBundlesAwareTrait;
   use Doctrine\ORM\Mapping as ORM;
   
   trait ProductTrait
   {
       use ProductBundlesAwareTrait;
   
    /**
     * @var ProductBundleInterface
     */
    #[ORM\OneToOne(
        targetEntity: 'BitBag\SyliusProductBundlePlugin\Entity\ProductBundleInterface',
        mappedBy: 'product',
        cascade: ['all'],
        orphanRemoval: true,
    )]
    protected $productBundle;
   
   }
   ```

   Mapping (XML) (Resources/config/doctrine/Product.Product.orm.xml):

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

7. Extend `OrderItem` (including Doctrine mapping):

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
   Mapping (Attributes) - Override bundle trait, by create new one and use it in Entity/Order/OrderItem .
   
   **Note.** If you're using Attributes Mapping, please use your `OrderItemTrait` in your `OrderItem` entity instead of plugins`ProductBundleOrderItemsAwareTrait`.

   ```php
   <?php 
   
   declare(strict_types=1);
   
   use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleOrderItemInterface;
   use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleOrderItemsAwareTrait;
   use Doctrine\Common\Collections\ArrayCollection;
   use Doctrine\ORM\Mapping as ORM;
   
   trait OrderItemTrait
   {
   use ProductBundleOrderItemsAwareTrait;
   
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
   Mapping (XML) (Resources/config/doctrine/Order.OrderItem.orm.xml):
   
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

9. Add configuration for extended product and order item:

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

10. If you have full configuration in xml override doctrine config:

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

11. Add plugin templates:
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
    cp vendor/bitbag/product-bundle-plugin/src/Resources/views/Admin/Order/Show/Summary/_item.html.twig templates/bundles/SyliusAdminBundle/Order/Show/Summary
    cp vendor/bitbag/product-bundle-plugin/src/Resources/views/Admin/Product/show.html.twig templates/bundles/SyliusAdminBundle/Product
    cp vendor/bitbag/product-bundle-plugin/src/Resources/views/Shop/Cart/Summary/_item.html.twig templates/bundles/SyliusShopBundle/Cart/Summary
    cp vendor/bitbag/product-bundle-plugin/src/Resources/views/Shop/Common/Order/Table/_item.html.twig templates/bundles/SyliusShopBundle/Common/Order/Table
    ```
    
12. Please clear application cache by running command below:

    ```bash
    bin/console cache:clear
    ```

13. Finish the installation by updating the database schema and installing assets:

    ```bash
    bin/console doctrine:migrations:migrate
    ```
14. Add plugin assets to your project:
[Import webpack config](./README_webpack-config.md)*

## Testing

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

## Functionalities 


All main functionalities of the plugin are described [here.](https://github.com/BitBagCommerce/SyliusProductBundlePlugin/blob/master/doc/functionalities.md)


# About us

---

BitBag is a company of people who **love what they do** and do it right. We fulfill the eCommerce technology stack with **Sylius**, Shopware, Akeneo, and Pimcore for PIM, eZ Platform for CMS, and VueStorefront for PWA. Our goal is to provide real digital transformation with an agile solution that scales with the **clients’ needs**. Our main area of expertise includes eCommerce consulting and development for B2C, B2B, and Multi-vendor Marketplaces.</br>
We are advisers in the first place. We start each project with a diagnosis of problems, and an analysis of the needs and **goals** that the client wants to achieve.</br>
We build **unforgettable**, consistent digital customer journeys on top of the **best technologies**. Based on a detailed analysis of the goals and needs of a given organization, we create dedicated systems and applications that let businesses grow.<br>
Our team is fluent in **Polish, English, German and, French**. That is why our cooperation with clients from all over the world is smooth.

**Some numbers from BitBag regarding Sylius:**
- 50+ **experts** including consultants, UI/UX designers, Sylius trained front-end and back-end developers,
- 120+ projects **delivered** on top of Sylius,
- 25+ **countries** of BitBag’s customers,
- 4+ **years** in the Sylius ecosystem.

**Our services:**
- Business audit/Consulting in the field of **strategy** development,
- Data/shop **migration**,
- Headless **eCommerce**,
- Personalized **software** development,
- **Project** maintenance and long term support,
- Technical **support**.

**Key clients:** Mollie, Guave, P24, Folkstar, i-LUNCH, Elvi Project, WestCoast Gifts.

---

If you need some help with Sylius development, don't be hesitated to contact us directly. You can fill the form on [this site](https://bitbag.io/contact-us/?utm_source=github&utm_medium=referral&utm_campaign=plugins_productbundle) or send us an e-mail at hello@bitbag.io!

---

[![](https://bitbag.io/wp-content/uploads/2021/08/sylius-badges-transparent-wide.png)](https://bitbag.io/contact-us/?utm_source=github&utm_medium=referral&utm_campaign=plugins_productbundle)

## Community

---- 

For online communication, we invite you to chat with us & other users on [Sylius Slack](https://sylius-devs.slack.com/).

# Demo Sylius Shop

---

We created a demo app with some useful use-cases of plugins!
Visit [sylius-demo.bitbag.io](https://sylius-demo.bitbag.io/) to take a look at it. The admin can be accessed under
[sylius-demo.bitbag.io/admin/login](https://sylius-demo.bitbag.io/admin/login) link and `bitbag: bitbag` credentials.
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
To learn more about our contribution workflow and more, we encourage you to use the following resources:
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

[![](https://bitbag.io/wp-content/uploads/2021/08/badges-bitbag.png)](https://bitbag.io/contact-us/?utm_source=github&utm_medium=referral&utm_campaign=plugins_productbundle)
