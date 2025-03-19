<?php

/**
 * Copyright (c) 2025 Czargroup Technologies. All rights reserved.
 *
 * @package Czargroup_ImportTracknumber
 * @author Czargroup Technologies
 */

namespace Czargroup\ImportTracknumber\Controller\Adminhtml\Orderindex;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use Czargroup\ImportTracknumber\Model\ResourceModel\Orderindex\CollectionFactory;
use Czargroup\ImportTracknumber\Model\Orderindex;

/**
 * Class Massdelete
 *
 * Handles mass deletion of order index records.
 */
class Massdelete extends Action
{
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var Orderindex
     */
    protected $orderindex;

    /**
     * Massdelete constructor.
     *
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param Orderindex $orderindex
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        Orderindex $orderindex
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->orderindex = $orderindex;
    }

    /**
     * Check if the user has permission to delete records.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Czargroup_ImportTracknumber::orderindex_delete');
    }

    /**
     * Execute the mass delete action.
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $recordDeleted = 0;

            foreach ($collection as $item) {
                $this->orderindex->load($item->getId());
                $this->orderindex->delete();
                $recordDeleted++;
            }

            $this->messageManager->addSuccessMessage(__('A total of %1 item(s) have been deleted.', $recordDeleted));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Error deleting items: %1', $e->getMessage()));
        }

        return $resultRedirect->setPath('*/*/');
    }
}
