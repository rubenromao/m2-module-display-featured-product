<?php
declare(strict_types=1);

namespace RubenRomao\DisplayFeaturedProduct\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Validator\ValidateException;
use Psr\Log\LoggerInterface;

/**
 * Create Featured Product Attribute
 */
class AttributeFeaturedProducts implements DataPatchInterface
{
    /**
     * AttributeFeaturedProducts constructor.
     *
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly ModuleDataSetupInterface $moduleDataSetup,
        private readonly EavSetupFactory $eavSetupFactory,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * Create a boolean attribute for featured products.
     *
     * @return void
     */
    public function apply(): void
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        try {
            $eavSetup->addAttribute(
                Product::ENTITY,
                'is_featured',
                [
                    'type' => 'int',
                    'label' => 'Featured Product',
                    'input' => 'boolean',
                    'default' => '1',
                    'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible' => true,
                    'used_in_product_listing' => true,
                    'user_defined' => true,
                    'required' => false,
                    'group' => 'General',
                    'use_in_product_listing' => '1',
                    'visible_on_front' => '1',
                    'is_used_in_grid' => '1',
                    'is_visible_in_grid' => '1',
                    'is_filterable_in_grid' => '1',
                    'is_searchable_in_grid' => '1',
                    'is_filterable' => '1',
                    'sort_order' => 80,
                ],
            );
        } catch (LocalizedException|ValidateException $e) {
            $this->logger->critical($e->getMessage());
        }
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getAliases(): array
    {
        return [];
    }
}
