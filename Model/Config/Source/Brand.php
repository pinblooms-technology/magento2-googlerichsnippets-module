<?php

/**
 * Copyright © PinBlooms Technology Pvt. Ltd. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace PinBlooms\GoogleRichSnippets\Model\Config\Source;

use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory;
use Magento\Framework\Option\ArrayInterface;

/**
 * Class Brand for provide brand value
 *
 * @var PinBlooms\GoogleRichSnippets\Model\Config\Source
 */
class Brand implements ArrayInterface
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory
     */
    protected $attributeCollectionFactory;

    /**
     * @param CollectionFactory $attributeCollectionFactory
     */
    public function __construct(
        CollectionFactory $attributeCollectionFactory
    ) {
        $this->attributeCollectionFactory = $attributeCollectionFactory;
    }

    /**
     * Return list of Product Attributes for Brand Options
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        $options = [];
        $options[] = [
            'value' => 0,
            'label' => __('Please select')
        ];

        $attributeCollection = $this->attributeCollectionFactory->create();
        $attributeCollection->addVisibleFilter()
            ->setOrder('frontend_label', 'ASC');

        foreach ($attributeCollection->getItems() as $attribute) {

            $options[] = [
                'value' => $attribute->getData('attribute_code'),
                'label' => $attribute->getData('frontend_label')
            ];
        }

        return $options;
    }
}
