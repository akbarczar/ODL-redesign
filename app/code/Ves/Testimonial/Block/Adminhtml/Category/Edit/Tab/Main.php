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

namespace Ves\Testimonial\Block\Adminhtml\Category\Edit\Tab;

class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;

    /**
     * @var \Ves\Testimonial\Model\ResourceModel\Testimonial\Collection
     */
    protected $_testimonialCollection;

    protected $_drawLevel;


    /**
     * @param \Magento\Backend\Block\Template\Context
     * @param \Magento\Framework\Registry
     * @param \Magento\Framework\Data\FormFactory
     * @param \Magento\Store\Model\System\Store
     * @param \Magento\Cms\Model\Wysiwyg\Config
     * @param array
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Ves\Testimonial\Model\ResourceModel\Testimonial\Collection $testimonialCollection,
        array $data = []
    ) {
        $this->_systemStore           = $systemStore;
        $this->_wysiwygConfig         = $wysiwygConfig;
        $this->_testimonialCollection = $testimonialCollection;
        parent::__construct($context, $registry, $formFactory, $data);

    }//end __construct()


    /**
     * Prepare form
     *
     * @return                                        $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        // @var $model \Magento\Cms\Model\Page
        $model = $this->_coreRegistry->registry('testimonial_category');

        /*
            * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('Ves_Testimonial::category_save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        $wysiwygConfig = $this->_wysiwygConfig->getConfig(['tab_id' => $this->getTabId().time()]);
        /*
            * @var \Magento\Framework\Data\Form $form
        */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('testimonial_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Testimonial Information')]);

        if ($model->getId()) {
            $fieldset->addField('category_id', 'hidden', ['name' => 'category_id']);
        }

        $fieldset->addField(
            'name',
            'text',
            [
             'name'     => 'name',
             'label'    => __('Name'),
             'title'    => __('Name'),
             'required' => true,
             'disabled' => $isElementDisabled,
            ]
        );
        $fieldset->addField(
            'is_active',
            'select',
            [
             'label'    => __('Status'),
             'title'    => __('Testimonial Status'),
             'name'     => 'is_active',
             'options'  => $model->getAvailableStatuses(),
             'disabled' => $isElementDisabled,
            ]
        );
        if (!$model->getId()) {
            $model->setData('is_active', $isElementDisabled ? '0' : '1');
        }

        $this->_eventManager->dispatch('adminhtml_testimonial_category_edit_tab_main_prepare_form', ['form' => $form]);

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();

    }//end _prepareForm()


    public function getCatCollection()
    {
        $model      = $this->_coreRegistry->registry('testimonial_category');
        $collection = $this->_categoryCollection
            ->addFieldToFilter('category_id', array('neq' => $model->getId()))
            ->setOrder('position');
        return $collection;

    }//end getCatCollection()


    public function getCats($categories, $cats = [], $level = 0)
    {
        foreach ($cats as $k => $v) {
        }

    }//end getCats()


    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Category Information');

    }//end getTabLabel()


    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Category Information');

    }//end getTabTitle()


    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;

    }//end canShowTab()


    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;

    }//end isHidden()


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
