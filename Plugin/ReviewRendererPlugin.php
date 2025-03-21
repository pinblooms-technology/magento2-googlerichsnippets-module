<?php

/**
 * Copyright © PinBlooms Technology Pvt. Ltd. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace PinBlooms\GoogleRichSnippets\Plugin;

use Magento\Review\Block\Product\ReviewRenderer as SubjectBlock;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Request\Http;

/**
 * Review Renderer Plugin class for review
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class ReviewRendererPlugin
{
    const XML_PATH_GOOGLESNIPPET_ENABLE_GOOGLE_SNIPPET = 'pinblooms_google_snippet/general/enable';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var Http
     */
    protected $request;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param Http $request
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Http $request
    ) {
        $this->request = $request;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Added item to the review - this is a fix for Luma theme
     *
     * @param SubjectBlock $subject
     * @param string $result
     * @return string
     */
    public function afterGetReviewsSummaryHtml(SubjectBlock $subject, $result = '')
    {
        $moduleName = $this->request->getModuleName();
        $enableGoogleSnippet = $this->scopeConfig->getValue(
            self::XML_PATH_GOOGLESNIPPET_ENABLE_GOOGLE_SNIPPET,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($enableGoogleSnippet && $moduleName == 'cms') {
            if ($result != '' && $subject->getRequest() !== null && $product = $subject->getProduct()) {
                // @codingStandardsIgnoreStart
                $result = '<div itemscope itemtype="https://schema.org/Product"><div itemprop="name" content="' . htmlspecialchars($product->getName()) . '"></div>' . $result . '</div>';
                // @codingStandardsIgnoreEnd
            }
        }
        return $result;
    }
}
