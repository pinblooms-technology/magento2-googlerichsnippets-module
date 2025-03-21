<?php

/**
 * Copyright © PinBlooms Technology Pvt. Ltd. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace PinBlooms\GoogleRichSnippets\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Price for price explanation
 *
 * @var PinBlooms\GoogleRichSnippets\Model\Config\Source
 */
class Price implements ArrayInterface
{

    /**
     * Return list of Price Options
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'incl_tax',
                'label' => __('Incl. Tax')
            ],
            [
                'value' => 'excl_tax',
                'label' => __('Excl. Tax')
            ]
        ];
    }
}
