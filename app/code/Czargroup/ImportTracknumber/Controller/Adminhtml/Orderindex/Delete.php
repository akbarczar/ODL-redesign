<?php

/**
 * Copyright (c) 2025 Czargroup Technologies. All rights reserved.
 *
 * @package Czargroup_ImportTracknumber
 * @author Czargroup Technologies
 */

namespace Czargroup\ImportTracknumber\Controller\Adminhtml\Orderindex;

use Magento\Backend\App\Action;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Czargroup\ImportTracknumber\Model\Orderindex;

/**
 * Class Delete
 *
 * Handles the deletion of an order record along with its associated file.
 */
class Delete extends Action
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var Orderindex
     */
    protected $orderindex;

    /**
     * Delete constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param Filesystem $filesystem
     * @param Orderindex $orderindex
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        Filesystem $filesystem,
        Orderindex $orderindex
    ) {
        parent::__construct($context);
        $this->filesystem = $filesystem;
        $this->orderindex = $orderindex;
    }

    /**
     * Execute the delete action.
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $exportedFile = $this->orderindex->load($id);

        if ($exportedFile->getId()) {
            try {
                $filePath = $exportedFile->getFilePath();
                $directory = $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);

                if ($directory->isFile($filePath)) {
                    $directory->delete($filePath);
                }

                $exportedFile->delete();
                $this->messageManager->addSuccessMessage(__('File record and file deleted successfully.'));
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        } else {
            $this->messageManager->addErrorMessage(__('File record not found.'));
        }

        return $this->_redirect('*/*/');
    }
}
