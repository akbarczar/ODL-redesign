<?php
/**
 * Venustheme
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://venustheme.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category  Venustheme
 * @package   Ves_Testimonial
 * @copyright Copyright (c) 2017 Landofcoder (http://www.venustheme.com/)
 * @license   http://www.venustheme.com/LICENSE-1.0.html
 */

namespace Ves\Testimonial\Model\Config\Source;

class Category implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\User\Model\UserFactory
     */
    protected $_categoryFactory;


    /**
     * @param \Lof\Testimonial\Model\Category
     */
    public function __construct(
        \Ves\Testimonial\Model\Category $categoryFactory
    ) {
        $this->_categoryFactory = $categoryFactory;

    }//end __construct()


    public function toOptionArray()
    {
        $options    = [];
        $collection = $this->_categoryFactory->getCollection();
        foreach ($collection as $_cat) {
            $options[] = [
                          'label' => $_cat->getName(),
                          'value' => $_cat->getCategoryId(),
                         ];
        }

        return $options;

    }//end toOptionArray()


}//end class
