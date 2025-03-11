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

namespace Ves\Testimonial\Block\Adminhtml\Category;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;


    /**
     * @param \Magento\Backend\Block\Widget\Context
     * @param \Magento\Framework\Registry
     * @param array
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);

    }//end __construct()


    /**
     * Initialize cms page edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId   = 'category_id';
        $this->_blockGroup = 'Ves_Testimonial';
        $this->_controller = 'adminhtml_category';

        parent::_construct();

        if ($this->_isAllowedAction('Ves_Testimonial::category_save')) {
            $this->buttonList->update('save', 'label', __('Save Category'));
            $this->buttonList->add(
                'saveandcontinue',
                [
                 'label'          => __('Save and Continue Edit'),
                 'class'          => 'save',
                 'data_attribute' => [
                                      'mage-init' => [
                                                      'button' => [
                                                                   'event'  => 'saveAndContinueEdit',
                                                                   'target' => '#edit_form',
                                                                  ],
                                                     ],
                                     ],
                ],
                -100
            );
        } else {
            $this->buttonList->remove('save');
        }//end if

        if ($this->_isAllowedAction('Ves_Testimonial::category_delete')) {
            $this->buttonList->update('delete', 'label', __('Delete Category'));
        } else {
            $this->buttonList->remove('delete');
        }

    }//end _construct()


    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('testimonial_category')->getId()) {
            return __("Edit Category '%1'", $this->escapeHtml($this->_coreRegistry->registry('testimonial_category')->getTitle()));
        } else {
            return __('New Category');
        }

    }//end getHeaderText()


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


    /**
     * Getter of url for "Save and Continue" button
     * tab_id will be replaced by desired by JS later
     *
     * @return string
     */
    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('cms/*/save', ['_current' => true, 'back' => 'edit', 'active_tab' => '{{tab_id}}']);

    }//end _getSaveAndContinueUrl()


    /**
     * Prepare layout
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _prepareLayout()
    {
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('page_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'page_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'page_content');
                }
            };
        ";

        return parent::_prepareLayout();

    }//end _prepareLayout()


}//end class
