<?php
declare(strict_types=1);

namespace RubenRomao\DisplayFeaturedProduct\ViewModel;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Media\Config;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollection;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Catalog\Helper\Image;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use RubenRomao\DisplayFeaturedProduct\Block\FeaturedProduct;
use RubenRomao\DisplayFeaturedProduct\Model\Config\Source\FeaturedSkusToOptionArray;

/**
 * View Model to get the featured product data to be used in the block.
 */
class FeaturedProductData implements ArgumentInterface
{
    /**
     * FeaturedProductData constructor.
     *
     * @param ProductRepositoryInterface $productRepository
     * @param ProductCollection $productCollection
     * @param Config $productMediaConfig
     * @param Image $imageHelper
     * @param ScopeConfigInterface $scopeConfig
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
        private readonly ProductCollection $productCollection,
        private readonly Config $productMediaConfig,
        private readonly Image $imageHelper,
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly StoreManagerInterface $storeManager,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * Here is where we do the sku check and respective actions.
     *
     * @return array|null
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function getFeaturedProduct(): ?array
    {
        /*
         * Get the featured product status and SKU from the configuration.
         */
        $featuredEnabled = $this->scopeConfig->getValue(
            FeaturedProduct::XML_PATH_FEATURED_PRODUCT_ENABLED,
            ScopeInterface::SCOPE_STORE,
        );
        $featuredSku = $this->scopeConfig->getValue(
            FeaturedProduct::XML_PATH_FEATURED_PRODUCT_SKU,
            ScopeInterface::SCOPE_STORE,
        );

        // Get the featured product if it is enabled and the SKU is set.
        if ($featuredEnabled && $featuredSku) {

            $productCollection = $this->productCollection->create();
            $productCollection->addAttributeToSelect('*')
                ->addAttributeToFilter('sku', ['eq' => $featuredSku])
                ->addMediaGalleryData()
                ->addFinalPrice()
                ->addUrlRewrite();

            $featuredProduct = null;

            foreach ($productCollection as $product) {
                $imageUrl = $this->imageHelper->init($product, 'product_base_image')->getUrl();
                /** @var Product $product */
                $featuredProduct = [
                    'sku' => $product->getSku(),
                    'name' => $product->getName(),
                    'description' => $product->getData('description'),
                    'price' => $product->getPrice(),
                    'product_url' => $product->getProductUrl(),
                    'image_url' => $imageUrl
                ];
            }

            return $featuredProduct;

        } else {
            $this->logger->error('Featured product is not enabled or SKU is not set.');
            return null;
        }
    }

    /**
     * Get the featured product image.
     *
     * @param $productSku
     * @return string
     * @throws NoSuchEntityException
     */
    Public function getProductImageUrl($productSku): string
    {
        $store = $this->storeManager->getStore();

        $product = $this->productRepository->get($productSku);

        //$productUrl = $product->getProductUrl();

        return $store->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) .
                  $this->productMediaConfig->getBaseMediaPath() .
                  $product->getImage();
    }
}
