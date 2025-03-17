<?php
namespace Mexbs\Fbshop\Controller\Adminhtml\Feed;


class GenerateFeed extends \Magento\Backend\App\Action
{
    protected $helper;
    protected $resultJsonFactory;

    public function __construct(
        \Mexbs\Fbshop\Helper\Data $helper,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Backend\App\Action\Context $context
    )
    {
        $this->helper = $helper;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $this->helper->scheduleFeedGeneration($this->getRequest()->getParam('store_id'));
    }
}