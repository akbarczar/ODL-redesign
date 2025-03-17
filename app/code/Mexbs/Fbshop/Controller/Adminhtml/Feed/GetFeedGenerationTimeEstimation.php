<?php
namespace Mexbs\Fbshop\Controller\Adminhtml\Feed;


class GetFeedGenerationTimeEstimation extends \Magento\Backend\App\Action
{
    protected $helper;
    protected $resultJsonFactory;

    public function __construct(
        \Mexbs\Fbshop\Helper\Data $helper,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Backend\App\Action\Context $context
    )
    {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->helper = $helper;
        parent::__construct($context);
    }

    public function execute()
    {
        return $this->resultJsonFactory->create()->setData([
            'numberOfProducts' => $this->helper->getNumberOfProductsToProcess(),
            'estimatedSecondsLeft' => $this->helper->getFeedGenerationTimeEstimation()
        ]);
    }
}