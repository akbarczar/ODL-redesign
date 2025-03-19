<?php

/**
 * Copyright (c) 2025 Czargroup Technologies. All rights reserved.
 *
 * @package Czargroup_ImportTracknumber
 * @author Czargroup Technologies
 */

namespace Czargroup\ImportTracknumber\Controller\Adminhtml\Orderindex;

use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Response\Http\FileFactory;
use Czargroup\ImportTracknumber\Model\Orderindex;

/**
 * Class Download
 *
 * Handles the download of exported order tracking files.
 */
class Download extends Action
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * @var Orderindex
     */
    protected $orderindex;

    /**
     * Download constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param Filesystem $filesystem
     * @param FileFactory $fileFactory
     * @param Orderindex $orderindex
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        Filesystem $filesystem,
        FileFactory $fileFactory,
        Orderindex $orderindex
    ) {
        parent::__construct($context);
        $this->filesystem = $filesystem;
        $this->fileFactory = $fileFactory;
        $this->orderindex = $orderindex;
    }

    /**
     * Execute the file download action.
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $exportedFile = $this->orderindex->load($id);

        if ($exportedFile->getId()) {
            $filePath = $exportedFile->getFilePath();
            $fileName = $exportedFile->getFileName();
            $directory = $this->filesystem->getDirectoryRead(DirectoryList::VAR_DIR);

            if ($directory->isFile($filePath)) {
                return $this->fileFactory->create(
                    $fileName,
                    [
                        'type' => 'filename',
                        'value' => $filePath,
                        'rm' => false
                    ],
                    DirectoryList::VAR_DIR
                );
            } else {
                $this->messageManager->addErrorMessage(__('File not found.'));
            }
        } else {
            $this->messageManager->addErrorMessage(__('File record not found.'));
        }

        return $this->_redirect('*/*/');
    }
}
