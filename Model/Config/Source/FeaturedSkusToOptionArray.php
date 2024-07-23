<?php
declare(strict_types=1);

namespace RubenRomao\DisplayFeaturedProduct\Model\Config\Source;

use Magento\Catalog\Model\Product;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollection;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\Product\Attribute\Source\Status;

/**
 * Create an array of featured products.
 */
class FeaturedSkusToOptionArray implements OptionSourceInterface
{
    /**
     * FeaturedSkusToOptionArray constructor.
     *
     * @param ProductCollection $productCollection
     */
    public function __construct(
        private readonly ProductCollection $productCollection,
    ) {
    }

    /**
     * Filter product collection to get available featured products.
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        $collection = $this->productCollection->create();
        $collection->addAttributeToSelect(['name', 'sku'])
            ->addAttributeToFilter('visibility', ['in' => Visibility::VISIBILITY_BOTH])
            ->addAttributeToFilter('status', ['eq' => Status::STATUS_ENABLED])
            ->addAttributeToFilter('is_featured', '1')
            ->addOrder('name', 'ASC');

        $options[] = ['label' => __('Please Select a Product'), 'value' => ''];

        /** @var Product $product */
        foreach ($collection as $product) {
            $options[] = [
                'value' => $product->getSku(),
                'label' => $product->getSku() . ' - ' . $product->getName(),
            ];
        }

        return $options;
    }
}
