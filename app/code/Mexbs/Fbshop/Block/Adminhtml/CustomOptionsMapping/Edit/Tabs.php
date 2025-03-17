<?php
namespace  Mexbs\Fbshop\Block\Adminhtml\CustomOptionsMapping\Edit;


class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('custom_options_mapping_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Custom Options Mapping'));
    }
}
