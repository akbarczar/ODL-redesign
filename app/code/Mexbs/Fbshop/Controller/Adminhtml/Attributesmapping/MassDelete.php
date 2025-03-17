<?php
namespace Mexbs\Fbshop\Controller\Adminhtml\Attributesmapping;

use Magento\Framework\Controller\ResultFactory;

class MassDelete extends \Magento\Backend\App\Action
{
    protected $mappingCollectionFactory;


    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Mexbs\Fbshop\Model\ResourceModel\AttributesMapping\CollectionFactory $mappingCollectionFactory
    ){
        $this->mappingCollectionFactory = $mappingCollectionFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $mappingIds = $this->getRequest()->getParam('mapping_id');
        if (!$mappingIds || !is_array($mappingIds)) {
            throw new \Exception(__('No mappings selected.'));
        }

        $mappingsCollection = $this->mappingCollectionFactory->create()
            ->addFieldToFilter("mapping_id", ["in" => $mappingIds]);
        $collectionSize = $mappingsCollection->getSize();

        foreach ($mappingsCollection as $mapping) {
            $mapping->delete();
        }

        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deleted.', $collectionSize));

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/index');
    }
}
