<?php

namespace Mexbs\Fbshop\Block\Adminhtml\CustomOptionsMapping\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Convert\DataObject as ObjectConverter;


class Main extends Generic implements TabInterface
{
    protected $fbFieldOptions;
    protected $productCustomOptionTitlesOptions;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Mexbs\Fbshop\Model\Mapping\Source\VariationsFbField $fbFieldOptions,
        \Mexbs\Fbshop\Model\Mapping\Source\ProductCustomOptionTitle $productCustomOptionTitlesOptions,
        array $data = []
    ) {
        $this->fbFieldOptions = $fbFieldOptions;
        $this->productCustomOptionTitlesOptions = $productCustomOptionTitlesOptions;

        parent::__construct(
            $context,
            $registry,
            $formFactory,
            $data
        );
    }

    public function getTabLabel()
    {
        return __('Custom Options Mapping Information');
    }

    public function getTabTitle()
    {
        return __('Custom Options Mapping Information');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }

    protected function _prepareForm()
    {
        $mapping = $this->_coreRegistry->registry('current_mapping');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('mapping_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => '']);

        if ($mapping->getId()) {
            $fieldset->addField('mapping_id', 'hidden', ['name' => 'id']);
        }


        $fieldset->addField(
            'fb_api_field_name',
            'select',
            [
                'name' => 'fb_api_field_name',
                'label' => __('Facebook API Field'),
                'title' => __('Facebook API Field'),
                'values' => $this->fbFieldOptions->toOptionArray(),
                'required' => true
            ]
        );

        $fieldset->addField(
            'attribute_code',
            'select',
            [
                'name' => 'custom_option_title',
                'label' => __('Custom Options Title'),
                'title' => __('Custom Options Title'),
                'values' => $this->productCustomOptionTitlesOptions->toOptionArray(),
                'required' => true,
                'note' => 'Only custom options which type is drop-down or radio buttons are listed here'
            ]
        );

        $form->setValues($mapping->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
