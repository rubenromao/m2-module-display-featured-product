<?php
declare(strict_types=1);

namespace RubenRomao\DisplayFeaturedProduct\ViewModel;

use Exception;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\Data\ProductSearchResultsInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Media\Config;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * View Model to get the featured product data to be used in the block.
 */
class FeaturedProductData implements ArgumentInterface
{
    public const string XML_PATH_FEATURED_PRODUCT_ENABLED = 'featured_products/homepage/enabled';
    public const string XML_PATH_FEATURED_PRODUCT_SKU = 'featured_products/homepage/featured_product_sku';
    public const string PRODUCT_PLACEHOLDER_IMAGE = 'placeholder/default/placeholder.jpg';

    /**
     * FeaturedProductData constructor.
     *
     * @param ProductRepositoryInterface $productRepository
     * @param SearchCriteriaBuilder $searchCriteria
     * @param Config $productMediaConfig
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param LoggerInterface $logger
     * @param string|null $errorMessage
     */
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
        private readonly SearchCriteriaBuilder $searchCriteria,
        private readonly Config $productMediaConfig,
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly StoreManagerInterface $storeManager,
        private readonly LoggerInterface $logger,
        private ?string $errorMessage,
    ) {
    }

    /**
     * Here is where we do the sku check and respective actions.
     *
     * @return ProductSearchResultsInterface|Exception|null
     */
    public function getFeaturedProduct(): ProductSearchResultsInterface|ManagerInterface|null
    {
        // Get the configuration values.
        $featuredEnabled = $this->getConfigValue(self::XML_PATH_FEATURED_PRODUCT_ENABLED);
        $featuredSku = $this->getConfigValue(self::XML_PATH_FEATURED_PRODUCT_SKU);

        /**
         * Stop the execution if the featured product is not enabled,
         *   or the SKU is not set and log the error and return null.
         */
        if (!$featuredEnabled || !$featuredSku) {
            $this->logger->error('Featured product is not enabled or SKU is not set.');
            return null;
        }

        try {
            // Search the featured product using the service contract ProductRepositoryInterface::getList().
            $searchProduct = $this->searchCriteria
                ->addFilter('sku', $featuredSku)
                ->addFilter('is_featured', 1)
                ->create();
            return $this->productRepository->getList($searchProduct);

        } catch (Exception $e) {
            /* If the search fails, log the error and display an error message. */
            $this->logger->critical('Error fetching the featured product: ' . $e->getMessage());
            $this->setErrorMessage('We are currently experiencing issues. Please try again later.');
            return null;
        }
    }

    /**
     * Get the product image URL.
     *
     * @param ProductInterface $product
     * @return string
     */
    public function getProductImageUrl(ProductInterface $product): string
    {
        try {
            $store = $this->storeManager->getStore();
            $imageUrl = $store->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) .
                $this->productMediaConfig->getBaseMediaPath() .
                $product->getImage();

            if (!$imageUrl) {
                $imageUrl = $this->productMediaConfig->getBaseMediaUrl() . self::PRODUCT_PLACEHOLDER_IMAGE;
            }

            return $imageUrl;

        } catch (Exception $e) {
            /* Log the error and return the product placeholder image URL. */
            $this->logger->error('Error fetching the product image: ' . $e->getMessage());
            return $this->productMediaConfig->getBaseMediaUrl() . self::PRODUCT_PLACEHOLDER_IMAGE;
        }
    }

    /**
     * Get the error message.
     *
     * @return string
     */
    public function getErrorMessage(): string
    {
        return $this->errorMessage ?? '';
    }

    /**
     * Set the error message.
     *
     * @param string $message
     */
    public function setErrorMessage(string $message): void
    {
        $this->errorMessage = $message;
    }

    /**
     * Get configuration value by path.
     *
     * @param string $path
     * @return mixed
     */
    private function getConfigValue(string $path): mixed
    {
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE);
    }
}
