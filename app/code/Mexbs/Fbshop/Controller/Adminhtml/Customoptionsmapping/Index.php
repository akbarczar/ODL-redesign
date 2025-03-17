<?php
namespace Mexbs\Fbshop\Controller\Adminhtml\Customoptionsmapping;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Mexbs_Fbshop::product_custom_options_mapping';

    protected $resultPageFactory;
    protected $helper;


    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }


    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Custom Options Mappings'));

        return $resultPage;
    }
}
