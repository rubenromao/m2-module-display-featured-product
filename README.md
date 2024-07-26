## Overview

The RubenRomao_DisplayFeaturedProduct module is designed to display a featured product on the frontend of a Magento 2 store.
It fetches product details, formats prices, and ensures compatibility with the Hyvä theme.

## Features

- Display a featured product on the frontend
- Fetch product details dynamically
- Format product prices
- Ensure compatibility with the Hyvä theme

## Requirements

- PHP 8.3 or later required
- Magento 2.4.7 or later

## Installation steps

Follow the steps below to install the RubenRomao_DisplayFeaturedProduct module:

1. Open your terminal
2. Navigate to your project folder: `cd /path-to-your-project-folder`
3. Install the module by typing: `composer require rubenromao/display-featured-product`
4. Enable the module: `bin/magento module:enable RubenRomao_DisplayFeaturedProduct`
5. Run setup upgrade: `bin/magento setup:upgrade`
6. Deploy static content: `bin/magento setup:static-content:deploy`
7. Clear the cache: `bin/magento cache:clean`

## Usage

After installing the RubenRomao_DisplayFeaturedProduct module, you can use it in the following way:

1. **Configuration:**
    - Go to `Stores > Configuration > Catalog > Featured Product`.
    - Enable the featured product and set the SKU of the product you want to feature.

2. **Template Integration:**
    - Use the provided template `featured_product_details.phtml` to display the featured product details.
    - For Hyvä theme compatibility, use `hyva-phtml-not-working_featured_product_details.phtml`.

## Extensibility

*You may provide guide here if your module can be extended or customized.*

## Support

If you encounter any problems while using this module, please [open an issue](link_to_your_project_issues_page) or
contact us directly at `support@example.com`.

## License

This project is licensed under the terms of the MIT license.

See [LICENSE](./LICENSE) for more details.
