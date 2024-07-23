<?php
declare(strict_types=1);

namespace RubenRomao\DisplayFeaturedProduct\Setup\Patch\Data;

use Magento\Cms\Model\PageFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Generate a custom homepage for desktop and mobile.
 */
class CustomHomepageDesktopAndMobile implements DataPatchInterface
{
    /**
     * CreateCustomHomepage constructor.
     *
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param PageFactory $pageFactory
     */
    public function __construct(
        private readonly ModuleDataSetupInterface $moduleDataSetup,
        private readonly PageFactory $pageFactory,
    ) {
    }

    /**
     * Create a custom CMS page.
     */
    public function apply(): void
    {
        $this->moduleDataSetup->startSetup();

        /* Create Desktop Homepage */
        $page = $this->pageFactory->create();
        $page->setTitle('Homepage 2columns-left')
            ->setIdentifier('custom-homepage')
            ->setIsActive(true)
            ->setPageLayout('2columns-left')
            ->setCustomLayoutUpdateXml('homepage.index.index.xml')
            ->setContent('Testing custom page content desktop.')
            ->save();

        /* Create Mobile Homepage */
        $page = $this->pageFactory->create();
        $page->setTitle('Homepage 1column')
            ->setIdentifier('custom-homepage-mobile')
            ->setIsActive(true)
            ->setPageLayout('1column')
            ->setCustomLayoutUpdateXml('homepagemobile.index.index.xml')
            ->setContent('Testing custom page content mobile.')
            ->save();

        $this->moduleDataSetup->endSetup();
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
