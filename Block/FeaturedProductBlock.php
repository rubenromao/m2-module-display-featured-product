<?php
declare(strict_types=1);

namespace RubenRomao\DisplayFeaturedProduct\Block;

use Magento\Framework\View\Element\Template;

class FeaturedProductBlock extends Template
{
    /**
     * XML paths to configuration settings.
     * I've put them here, so they can be easily reused.
     */
    public const string XML_PATH_FEATURED_PRODUCT_ENABLED = 'featured_products/homepage/enabled';
    public const string XML_PATH_FEATURED_PRODUCT_SKU = 'featured_products/homepage/featured_product_sku';
}
