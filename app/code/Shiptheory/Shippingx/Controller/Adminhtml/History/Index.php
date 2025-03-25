<?php

namespace Shiptheory\Shippingx\Controller\Adminhtml\History;

class Index extends \Magento\Backend\App\AbstractAction
{

    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Shiptheory History'));
        $this->_view->renderLayout();
    }

    protected function _isAllowed()
    {
        return true;
    }
}
