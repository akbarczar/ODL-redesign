<?php
namespace Mexbs\Fbshop\Controller\Adminhtml\Customoptionsmapping;

class NewAction extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Mexbs_Fbshop::product_custom_options_mapping';

    public function execute()
    {
        $this->_forward('edit');
    }
}

