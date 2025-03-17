<?php
namespace Mexbs\Fbshop\Block\Adminhtml\Logs\Grid;

class Container extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_fbshop_logs';
        $this->_headerText = __('Feed Generation Logs');
        parent::_construct();
        $this->removeButton('add');
    }
}
