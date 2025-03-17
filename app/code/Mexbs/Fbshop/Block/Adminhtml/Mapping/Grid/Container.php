<?php
namespace Mexbs\Fbshop\Block\Adminhtml\Mapping\Grid;

class Container extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_fbshop_mappings';
        $this->_headerText = __('Product Attributes Mapping');
        $this->_addButtonLabel = __('Add New Mapping');
        parent::_construct();
    }
}
