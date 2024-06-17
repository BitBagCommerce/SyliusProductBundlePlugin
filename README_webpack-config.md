# BitBag SyliusProductBundlePlugin

## Installation - Import Webpack Config

<br>

1. Import plugin's `webpack.config.js` file

```js
// webpack.config.js
const [bitbagproductBundleShop, bitbagproductBundleAdmin] = require('./vendor/bitbag/product-bundle-plugin/webpack.config.js');
...

module.exports = [..., bitbagproductBundleShop, bitbagproductBundleAdmin];
```

2. Add new packages in `./config/packages/assets.yaml`

```yml
# config/packages/assets.yaml

framework:
    assets:
        packages:
            # ...
            shop:
                json_manifest_path: '%kernel.project_dir%/public/build/shop/manifest.json'
            product_bundle_shop:
                json_manifest_path: '%kernel.project_dir%/public/build/bitbag/productBundle/shop/manifest.json'
            product_bundle_admin:
                json_manifest_path: '%kernel.project_dir%/public/build/bitbag/productBundle/admin/manifest.json'
```

3. Add new build paths in `./config/packages/webpack_encore.yml`

```yml
# config/packages/webpack_encore.yml

webpack_encore:
    builds:
        # ...
        product_bundle_shop: '%kernel.project_dir%/public/build/bitbag/productBundle/shop'
        product_bundle_admin: '%kernel.project_dir%/public/build/bitbag/productBundle/admin'
```

4. Add encore functions to your templates

```twig
{# @SyliusShopBundle/_scripts.html.twig #}
{{ encore_entry_script_tags('bitbag-productBundle-shop', null, 'product_bundle_shop') }}

{# @SyliusShopBundle/_styles.html.twig #}
{{ encore_entry_link_tags('bitbag-productBundle-shop', null, 'product_bundle_shop') }}

{# @SyliusAdminBundle/_scripts.html.twig #}
{{ encore_entry_script_tags('bitbag-productBundle-admin', null, 'product_bundle_admin') }}

{# @SyliusAdminBundle/_styles.html.twig #}
{{ encore_entry_link_tags('bitbag-productBundle-admin', null, 'product_bundle_admin') }}
```

5. Run `yarn encore dev` or `yarn encore production`
