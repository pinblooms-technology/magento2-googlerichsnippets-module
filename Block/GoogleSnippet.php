<?php

/**
 * Copyright © PinBlooms Technology Pvt. Ltd. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace PinBlooms\GoogleRichSnippets\Block;

use Magento\Review\Model\ResourceModel\Review\Collection as ReviewCollection;
use Magento\Framework\View\Element\Template;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\CatalogUrlRewrite\Model\CategoryUrlRewriteGenerator;
use Magento\Catalog\Block\Product\Context as productContext;
use Magento\Catalog\Block\Product\ImageBuilder;
use Magento\Catalog\Block\Product\Image;
use Magento\Cms\Model\Page;
use Magento\Review\Model\ResourceModel\Review\CollectionFactory;
use Magento\Review\Model\ResourceModel\Rating\Option\Vote\CollectionFactory as voteCollectionFactory;
use Magento\Review\Model\Review\Summary;
use Magento\Review\Model\Review\SummaryFactory;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Registry;
use PinBlooms\GoogleRichSnippets\Helper\Data;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;

class GoogleSnippet extends Template
{
    /**
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * @var ImageBuilder
     */
    protected $_imageBuilder;

    /**
     * @var Data
     */
    protected $_helper;

    /**
     * Block factory
     *
     * @var SummaryFactory
     */
    protected $_reviewSummaryFactory;

    /**
     * Review collection
     *
     * @var ReviewCollection
     */
    protected $_reviewsCollection;

    /**
     * Review resource model
     *
     * @var CollectionFactory
     */
    protected $_reviewsColFactory;

    /**
     * Review vote collection
     *
     * @var voteCollectionFactory
     */
    protected $_voteCollection;

    /**
     * @var UrlFinderInterface
     */
    private $urlFinder;

    /**
     * @var Page
     */
    protected $_page;

    /**
     * @param productContext $productContext
     * @param Data $helper
     * @param SummaryFactory $reviewSummaryFactory
     * @param CollectionFactory $collectionFactory
     * @param voteCollectionFactory $voteCollection
     * @param UrlFinderInterface $urlFinder
     * @param Page $page
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        productContext $productContext,
        Data $helper,
        SummaryFactory $reviewSummaryFactory,
        CollectionFactory $collectionFactory,
        voteCollectionFactory $voteCollection,
        UrlFinderInterface $urlFinder,
        Page $page,
        Context $context,
        array $data = []
    ) {
        $this->_coreRegistry = $productContext->getRegistry();
        $this->_helper = $helper;
        $this->_reviewSummaryFactory = $reviewSummaryFactory;
        $this->_imageBuilder = $productContext->getImageBuilder();
        $this->_reviewsColFactory = $collectionFactory;
        $this->_voteCollection = $voteCollection;
        $this->urlFinder = $urlFinder;
        $this->_page = $page;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve current product model
     *
     * @return Product
     */
    public function getProduct()
    {
        return $this->_coreRegistry->registry('product');
    }

    /**
     * Retrieve product image
     *
     * @param Product $product
     * @param string $imageId
     * @param array $attributes
     * @return Image
     */
    public function getImage($product, $imageId, $attributes = [])
    {
        return $this->_imageBuilder->setProduct($product)
            ->setImageId($imageId)
            ->setAttributes($attributes)
            ->create();
    }

    /**
     * GetDescription function
     *
     * @param Product $product
     * @return void
     */
    public function getDescription($product)
    {
        if ($this->_helper->getDescriptionType()) {
            return nl2br($product->getData('description') ?? '');
        } else {
            return nl2br($product->getData('short_description') ?? '');
        }
    }

    /**
     * GetBrand function
     *
     * @param Product $product
     * @return void
     */
    public function getBrand($product)
    {
        $brandAttribute = $this->_helper->getBrand();

        if ($brandAttribute == 'category_ids') {
            $categoryNames = [];
            $categories = $product->getCategoryCollection()
                ->addAttributeToSelect('name');
            foreach ($categories as $category) {
                $categoryNames[] = $category->getName();
            }

            return implode(',', $categoryNames);
        }

        $brandName = '';
        if ($brandAttribute) {
            try {
                $brandName = $product->getAttributeText($brandAttribute);
                if (is_array($brandName) || !$brandName) {
                    $brandName = $product->getData($brandAttribute);
                }
            } catch (\Exception $ex) {
                $brandName = '';
            }
        }
        return $brandName;
    }

    /**
     * GetSku function
     *
     * @param Product $product
     * @return void
     */
    public function getSku($product)
    {
        $skuAttribute = $this->_helper->getSku();
        $sku = '';
        if ($skuAttribute) {
            try {
                $sku = $product->getAttributeText($skuAttribute);
                if (is_array($sku) || !$sku) {
                    $sku = $product->getData($skuAttribute);
                }
            } catch (\Exception $ex) {
                $sku = '';
            }
        }
        return $sku;
    }

    /**
     * GetReviewSummary function
     *
     * @return void
     */
    public function getReviewSummary()
    {
        $storeId = $this->_storeManager->getStore()->getId();
        $reviewSummary = $this->_reviewSummaryFactory->create();
        $reviewSummary->setData('store_id', $storeId);
        $summaryModel = $reviewSummary->load($this->getProduct()->getId());

        return $summaryModel;
    }

    /**
     * Get collection of reviews
     *
     * @return ReviewCollection
     */
    public function getReviewsCollection()
    {
        if (null === $this->_reviewsCollection) {
            $this->_reviewsCollection = $this->_reviewsColFactory->create()
                ->addStoreFilter(
                    $this->_storeManager->getStore()->getId()
                )->addStatusFilter(
                    \Magento\Review\Model\Review::STATUS_APPROVED
                )->addEntityFilter(
                    'product',
                    $this->getProduct()->getId()
                )->setDateOrder();
        }
        return $this->_reviewsCollection;
    }

    /**
     * Get collection of review's rating
     *
     * @return ReviewRatingCollection
     */
    /**
     * Get collection of review's rating
     *
     * @param int $reviewId
     * @return void
     */
    public function getReviewRating($reviewId)
    {
        $rating = $this->_voteCollection->create();
        $rating->addRatingInfo()->addOptionInfo()->addRatingOptions()
            ->addFieldToFilter('review_id', $reviewId);
        $aggregate = 0;
        $totalReview = count($rating->getData());
        foreach ($rating->getData() as $rate) {
            $aggregate += $rate['percent'];
        }
        return $aggregate / ($totalReview * 20);
    }

    /**
     * Function for get Currency Code
     *
     * @return string
     */
    public function getCurrencyCode()
    {
        return $this->_storeManager->getStore()->getCurrentCurrencyCode();
    }

    /**
     * Function for get Currency URL
     *
     * @return string
     */
    public function getCurrentUrl()
    {
        return $this->_urlBuilder->getCurrentUrl();
    }

    /**
     * Function for get Product Price
     *
     * @return float
     */
    public function getPrice()
    {
        $priceOption = $this->_helper->getGoogleSnippetPrice();
        return $this->_calculatePrice($priceOption);
    }

    /**
     * Funtion for Calculate Price
     *
     * @param string $priceOption
     * @return float
     */
    protected function _calculatePrice($priceOption)
    {
        $priceInfo = $this->getProduct()->getPriceInfo()
            ->getPrice('final_price')->getAmount();
        $price = $priceInfo->getValue();
        /** Display of both prices incl. tax and excl. tax */
        if ((int)$this->_scopeConfig->getValue(
            'tax/display/type',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        ) === 3) {
            switch ($priceOption) {
                case 'incl_tax':
                    $price = $priceInfo->getValue();
                    break;
                case 'excl_tax':
                    $price = $priceInfo->getValue('tax');
                    break;
            }
        }
        return number_format($price, 2, '.', '');
    }

    /**
     * Get page
     *
     * @return string
     */
    protected function getPage()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return $this->getRequest()->getFullActionName();
    }

    /**
     * Retrieve current category model
     *
     * @return Category|null
     */
    protected function getCategory()
    {
        return $this->_coreRegistry->registry('current_category');
    }

    /**
     * Get category url
     *
     * @param Category $category
     * @return string
     */
    public function getFinalUrlFromCategory(Category $category)
    {
        if ($category->hasData('request_path') && $category->getRequestPath() != '') {
            $category->setData('url', $category->getUrlInstance()->getDirectUrl($category->getRequestPath()));
            return $category->getData('url');
        }

        $rewrite = $this->urlFinder->findOneByData([
            UrlRewrite::ENTITY_ID => $category->getId(),
            UrlRewrite::ENTITY_TYPE => CategoryUrlRewriteGenerator::ENTITY_TYPE,
            UrlRewrite::STORE_ID => $category->getStoreId(),
            UrlRewrite::REDIRECT_TYPE => 0, // Final URL, no redirect. TD-CHANGE from origin
        ]);
        if ($rewrite) {
            $category->setData('url', $category->getUrlInstance()->getDirectUrl($rewrite->getRequestPath()));
            return $category->getData('url');
        }

        $category->setData('url', $category->getCategoryIdUrl());
        return $category->getData('url');
    }

    /**
     * Get breadcrumb schema depending on page
     *
     * @return array|bool
     * @throws NoSuchEntityException
     */
    public function getSchemaBreadcrumb()
    {
        if ($this->getPage() == 'catalog_category_view') {
            if (!$this->_helper->getSchemaEnableBreadcrumb()) {
                return false;
            }
            $schema = $this->getCategorySchemaBreadcrumb($this->getPage());
        } elseif ($this->getPage() == 'catalog_product_view') {
            if (!$this->_helper->getSchemaEnableProductBreadcrumb()) {
                return false;
            }
            $schema = $this->getProductSchemaBreadcrumb();
        } else {
            if (!$this->_helper->getSchemaEnableCMSBreadcrumb()) {
                return false;
            }
            $schema = $this->getPageSchemaBreadcrumb();
        }
        return is_array($schema) ? $schema : false;
    }

    /**
     * Get category schema
     *
     * @param bool $catalogPage
     * @return array|bool
     */
    protected function getCategorySchemaBreadcrumb($catalogPage)
    {
        // set category schema
        $schema = [
            '@context' => 'https://schema.org/',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [],
        ];

        $category = $this->getCategoryForBreadcrumb($catalogPage);
        if (!$category) {
            return false;
        }
        $crumbs = $this->getBreadcrumbPath($category);
        $cat = 'category' . $this->getCategory()->getId();
        if (isset($crumbs['category' . $this->getCategory()->getId()])) {
            $crumbs[$cat]['link'] = $this->getFinalUrlFromCategory($this->getCategory());
        }

        $schemaCrumb = [];
        foreach ($crumbs as $crumb) {
            $schemaCrumb[] = [
                '@type' => 'ListItem',
                'position' => (count($schemaCrumb) + 1),
                'item' => [
                    '@id' => $crumb['link'],
                    'name' => $crumb['label'],
                ]
            ];
        }
        $schema['itemListElement'] = $schemaCrumb;

        // return schema
        return $schema;
    }

    /**
     * Return current category path or get it from current category
     *
     * Creating array of categories paths for breadcrumbs
     *
     * @param Category $category
     * @return array
     */
    public function getBreadcrumbPath(Category $category)
    {
        $path['home'] = [
            'label' => 'Home',
            'link' => $this->_storeManager->getStore()->getBaseUrl()
        ];
        if ($category) {
            $pathInStore = $category->getPathInStore();
            $pathIds = array_reverse(explode(',', $pathInStore));

            $categories = $category->getParentCategories();

            // add category path breadcrumb
            foreach ($pathIds as $categoryId) {
                if (isset($categories[$categoryId]) && $categories[$categoryId]->getName()) {
                    $path['category' . $categoryId] = [
                        'label' => $categories[$categoryId]->getName(),
                        'link' => $this->getFinalUrlFromCategory($categories[$categoryId])
                    ];
                }
            }
        }
        return $path;
    }

    /**
     * @param bool $catalogPage
     * @return Category|null
     */

    /**
     * GetCategoryForBreadcrumb function
     *
     * @param string $catalogPage
     * @return void
     */
    protected function getCategoryForBreadcrumb($catalogPage)
    {
        $category = $this->getCategory();
        if ($catalogPage && $category === null) {
            $category = $this->getProduct()->getCategory();
        }
        return $category;
    }

    /**
     * Get CMS Page schema
     *
     * @return array|bool
     */
    protected function getPageSchemaBreadcrumb()
    {
        // set Page schema
        $schema = [
            '@context' => 'https://schema.org/',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [],
        ];

        $page = $this->getPageForBreadcrumb();
        if (!$page) {
            return false;
        }

        $schemaCrumb[] = [
            '@type' => 'ListItem',
            'position' => 1,
            'item' => [
                '@id' => $this->_storeManager->getStore()->getBaseUrl(),
                'name' => "Home",
            ]
        ];

        $schema['itemListElement'] = $schemaCrumb;

        return $schema;
    }

    /**
     * GetPageForBreadcrumb function
     *
     * @return Page|null
     */
    protected function getPageForBreadcrumb()
    {
        return $this->_page->load($this->_page->getId());
    }

    /**
     * Get product schema
     *
     * @return array|bool
     */
    protected function getProductSchemaBreadcrumb()
    {
        // set product schema
        $schema = [
            '@context' => 'https://schema.org/',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [],
        ];

        $crumbs = $this->getProductBreadcrumbPath();

        $schemaCrumb = [];
        foreach ($crumbs as $crumb) {
            $schemaCrumb[] = [
                '@type' => 'ListItem',
                'position' => (count($schemaCrumb) + 1),
                'item' => [
                    '@id' => $crumb['link'],
                    'name' => $crumb['label'],
                ]
            ];
        }
        $schema['itemListElement'] = $schemaCrumb;

        return $schema;
    }

    /**
     * Return current product breadcrumb path
     *
     * @return array
     */
    public function getProductBreadcrumbPath()
    {
        $breadcrumbs = [];

        $breadcrumbs[] = [
            'label' => 'Home',
            'link' => $this->_storeManager->getStore()->getBaseUrl()
        ];

        $product = $this->_coreRegistry->registry('current_product');
        $categoryCollection = clone $product->getCategoryCollection();
        $categoryCollection->clear();
        $categoryCollection->addAttributeToSort('level', $categoryCollection::SORT_ORDER_DESC)
            ->addAttributeToFilter(
                'path',
                [
                    'like' => "1/" . $this->_storeManager->getStore()
                        ->getRootCategoryId() . "/%"
                ]
            );
        $categoryCollection->setPageSize(1);
        $breadcrumbCategories = $categoryCollection->getFirstItem()->getParentCategories();
        foreach ($breadcrumbCategories as $category) {
            $breadcrumbs[] = [
                'label' => $category->getName(),
                'link' => $category->getUrl()
            ];
        }
        $breadcrumbs[] = [
            'label' => $product->getName(),
            'link' => ''
        ];

        return $breadcrumbs;
    }
}
