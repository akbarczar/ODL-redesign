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

namespace Ves\Testimonial\Block\Adminhtml;

class Category extends \Magento\Backend\Block\Widget\Grid\Container
{


    /**
     * Block constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_category';
        $this->_blockGroup = 'Ves_Testimonial';
        $this->_headerText = __('Manage Category');

        parent::_construct();

        if ($this->_isAllowedAction('Ves_Testimonial::save')) {
            $this->buttonList->update('add', 'label', __('Add New Category'));
        } else {
            $this->buttonList->remove('add');
        }

    }//end _construct()


    /**
     * Check permission for passed action
     *
     * @param  string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);

    }//end _isAllowedAction()


}//end class
