<?php
/**
 * Copyright © PinBlooms Technology Pvt. Ltd. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace PinBlooms\GoogleRichSnippets\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Shortcuts container
 *
 * Description Class
 *
 */
class Description implements ArrayInterface
{
    /**
     * Return list of Description Options
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return [
                [
                    'value' => '0',
                    'label' => __('Short Description')
                ],
                [
                    'value' => '1',
                    'label' => __('Long Description')
                ]
            ];
    }
}
