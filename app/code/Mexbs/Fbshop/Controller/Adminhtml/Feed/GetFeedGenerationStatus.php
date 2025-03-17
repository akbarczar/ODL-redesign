<?php
namespace Mexbs\Fbshop\Controller\Adminhtml\Feed;


class GetFeedGenerationStatus extends \Magento\Framework\App\Action\Action
{
    protected $helper;
    protected $resultJsonFactory;

    public function __construct(
        \Mexbs\Fbshop\Helper\Data $helper,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\App\Action\Context $context
    )
    {
        $this->helper = $helper;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        return $this->resultJsonFactory->create()->setData([
            'statusMessage' => $this->helper->getStatusMessage(),
            'is_generation_in_progress' => $this->helper->getIsGenerationInProgress(),
            'is_generation_scheduled' => $this->helper->isGenerationScheduled(),
            'feed_stores_data' => $this->helper->getFeedStoresDataForFrontend()
        ]);
    }
}