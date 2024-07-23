<?php
declare(strict_types=1);

namespace RubenRomao\DisplayFeaturedProduct\ViewModel;

use Magento\Catalog\Api\Data\ProductSearchResultsInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Media\Config;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use RubenRomao\DisplayFeaturedProduct\Block\FeaturedProductBlock;

/**
 * View Model to get the featured product data to be used in the block.
 */
class FeaturedProductData implements ArgumentInterface
{
    /**
     * FeaturedProductData constructor.
     *
     * @param ProductRepositoryInterface $productRepository
     * @param SearchCriteriaBuilder $searchCriteria
     * @param Config $productMediaConfig
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
        private readonly SearchCriteriaBuilder $searchCriteria,
        private readonly Config $productMediaConfig,
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly StoreManagerInterface $storeManager,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * Here is where we do the sku check and respective actions.
     *
     * @return ProductSearchResultsInterface|null
     */
    public function getFeaturedProduct(): ?ProductSearchResultsInterface
    {
        /*
         * Get the featured product status and SKU from the configuration.
         */
        $featuredEnabled = $this->scopeConfig->getValue(
            FeaturedProductBlock::XML_PATH_FEATURED_PRODUCT_ENABLED,
            ScopeInterface::SCOPE_STORE,
        );
        $featuredSku = $this->scopeConfig->getValue(
            FeaturedProductBlock::XML_PATH_FEATURED_PRODUCT_SKU,
            ScopeInterface::SCOPE_STORE,
        );

        // Get the featured product if it is enabled and the SKU is set.
        if ($featuredEnabled && $featuredSku) {
            $searchProduct = $this->searchCriteria
                ->addFilter('sku', $featuredSku, 'eq')
                ->addFilter('is_featured', 1, 'eq')
                ->create();
            return $this->productRepository->getList($searchProduct);
        } else {
            $this->logger->error('Featured product is not enabled or SKU is not set.');
            return null;
        }
    }

    /**
     * Get the featured product URL image by SKU.
     *
     * @param string $productSku
     * @return string
     * @throws NoSuchEntityException
     */
    public function getProductImageUrl(string $productSku): string
    {
        $product = $this->productRepository->get($productSku);
        $store = $this->storeManager->getStore();

        return $store->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) .
                  $this->productMediaConfig->getBaseMediaPath() .
                  $product->getImage();
    }

    /**
     * Get the product detail page URL by SKU.
     *
     * @param string $productSku
     * @return string
     * @throws NoSuchEntityException
     */
    public function getProductDetailPageUrl(string $productSku): string
    {
        $product = $this->productRepository->get($productSku);
        return $product->getProductUrl();
    }
}
