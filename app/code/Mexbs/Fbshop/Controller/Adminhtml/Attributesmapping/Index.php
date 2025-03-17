<?php
namespace Mexbs\Fbshop\Controller\Adminhtml\Attributesmapping;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action
{
    protected $resultPageFactory;
    protected $helper;


    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Mexbs\Fbshop\Helper\Data $helper
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->helper = $helper;
    }


    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Mappings'));

        $missingFieldsErrorMessage = $this->helper->getMissingFieldsErrorMessage();

        if($missingFieldsErrorMessage){
            $this->messageManager->addErrorMessage($missingFieldsErrorMessage);
        }


        return $resultPage;
    }
}
