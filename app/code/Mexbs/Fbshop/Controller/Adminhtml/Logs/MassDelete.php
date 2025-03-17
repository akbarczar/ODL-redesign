<?php
namespace Mexbs\Fbshop\Controller\Adminhtml\Logs;

use Magento\Framework\Controller\ResultFactory;

class MassDelete extends \Magento\Backend\App\Action
{
    protected $logsCollectionFactory;


    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Mexbs\Fbshop\Model\ResourceModel\Log\CollectionFactory $logsCollectionFactory
    ){
        $this->logsCollectionFactory = $logsCollectionFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $logIds = $this->getRequest()->getParam('log_id');
        if (!$logIds || !is_array($logIds)) {
            throw new \Exception(__('No logs selected.'));
        }

        $this->_view->getLayout()->initMessages();

        $logsCollection = $this->logsCollectionFactory->create()
            ->addFieldToFilter("log_id", ["in" => $logIds]);
        $collectionSize = $logsCollection->getSize();

        foreach ($logsCollection as $log) {
            $log->delete();
        }

        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deleted.', $collectionSize));

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('fbshop/logs/index');
    }
}