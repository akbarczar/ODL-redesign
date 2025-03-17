<?php
namespace Mexbs\Fbshop\Controller\Adminhtml\Attributesmapping;

class NewAction extends \Magento\Backend\App\Action
{
    public function execute()
    {
        $this->_forward('edit');
    }
}

