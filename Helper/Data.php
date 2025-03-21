<?php

/**
 * Copyright © PinBlooms Technology Pvt. Ltd. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace PinBlooms\GoogleRichSnippets\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Helper\Context;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Data extends AbstractHelper
{
    const XML_PATH_GOOGLESNIPPET_ENABLE_GOOGLE_SNIPPET = 'pinblooms_google_snippet/general/enable';

    const XML_PATH_GOOGLESNIPPET_PINBLOOMS_GOOGLE_SNIPPET = 'pinblooms_google_snippet';

    const XML_PATH_GOOGLESNIPPET_DESCRIPTION_GOOGLE_SNIPPET = 'pinblooms_google_snippet/general/description';

    const XML_PATH_GOOGLESNIPPET_BRAND_GOOGLE_SNIPPET = 'pinblooms_google_snippet/general/brand';

    const XML_PATH_GOOGLESNIPPET_SKU_GOOGLE_SNIPPET = 'pinblooms_google_snippet/general/sku';

    const XML_PATH_GOOGLESNIPPET_PRICE_GOOGLE_SNIPPET = 'pinblooms_google_snippet/general/price';

    /**
     * @var array
     */
    protected $_cardsOptions;

    /**
     * Constructor
     *
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        parent::__construct($context);

        $this->_cardsOptions = $this->scopeConfig->getValue(
            self::XML_PATH_GOOGLESNIPPET_PINBLOOMS_GOOGLE_SNIPPET,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * GetDescriptionType function
     *
     * @param integer $storeId
     * @return void
     */
    public function getDescriptionType($storeId = 0)
    {
        if ($storeId) {
            return $this->scopeConfig->getValue(
                self::XML_PATH_GOOGLESNIPPET_DESCRIPTION_GOOGLE_SNIPPET,
                ScopeInterface::SCOPE_STORE,
                $storeId
            ) ?? '';
        } else {
            return $this->_cardsOptions['general']['description'] ?? '';
        }
    }

    /**
     * GetBrand function
     *
     * @param integer $storeId
     * @return mixed
     */
    public function getBrand($storeId = 0)
    {
        if ($storeId) {
            return $this->scopeConfig->getValue(
                self::XML_PATH_GOOGLESNIPPET_BRAND_GOOGLE_SNIPPET,
                ScopeInterface::SCOPE_STORE,
                $storeId
            ) ?? '';
        } else {
            return $this->_cardsOptions['general']['brand'] ?? '';
        }
    }

    /**
     * GetSku function
     *
     * @param integer $storeId
     * @return mixed
     */
    public function getSku($storeId = 0)
    {
        if ($storeId) {
            return $this->scopeConfig->getValue(
                self::XML_PATH_GOOGLESNIPPET_SKU_GOOGLE_SNIPPET,
                ScopeInterface::SCOPE_STORE,
                $storeId
            ) ?? '';
        } else {
            return $this->_cardsOptions['general']['sku'] ?? '';
        }
    }

    /**
     * GetGoogleSnippetPrice function
     *
     * @param integer $storeId
     * @return mixed
     */
    public function getGoogleSnippetPrice($storeId = 0)
    {
        if ($storeId) {
            return $this->scopeConfig->getValue(
                self::XML_PATH_GOOGLESNIPPET_PRICE_GOOGLE_SNIPPET,
                ScopeInterface::SCOPE_STORE,
                $storeId
            ) ?? '';
        } else {
            return $this->_cardsOptions['general']['price'] ?? '';
        }
    }

    /**
     * Check if category breadcrumb schema is enabled
     *
     * @param int $storeId
     * @return bool
     */
    public function getSchemaEnableBreadcrumb($storeId = 0)
    {
        return (bool)$this->getConfig($storeId, 'enable_breadcrumb', 'general');
    }

    /**
     * Check if CMS breadcrumb schema is enabled
     *
     * @param int $storeId
     * @return bool
     */
    public function getSchemaEnableCMSBreadcrumb($storeId = 0)
    {
        return (bool)$this->getConfig($storeId, 'enable_cms_breadcrumb', 'general');
    }

    /**
     * Check if product breadcrumb schema is enabled
     *
     * @param int $storeId
     * @return bool
     */
    public function getSchemaEnableProductBreadcrumb($storeId = 0)
    {
        return (bool)$this->getConfig($storeId, 'enable_product_breadcrumb', 'general');
    }

    /**
     * Returns system configuration
     *
     * @param int $storeId int store id
     * @param string $name string config name
     * @param string $second
     * @return mixed
     */
    protected function getConfig($storeId, $name, $second)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GOOGLESNIPPET_PINBLOOMS_GOOGLE_SNIPPET . '/' . $second . '/' . $name,
            ScopeInterface::SCOPE_STORE,
            !empty($storeId) ? $storeId : null
        );
    }
}
