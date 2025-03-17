<?php
namespace  Mexbs\Fbshop\Block\Adminhtml\Mapping\Edit;


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
        $this->setId('mapping_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Mapping'));
    }
}
