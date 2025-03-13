<?php

/**
 * Copyright (c) 2025 Czargroup Technologies. All rights reserved.
 *
 * @package Czargroup_Export
 * @author Czargroup Technologies
 */

declare(strict_types=1);

namespace Czargroup\Export\Plugin\Magento\ImportExport\Controller\Adminhtml;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\MessageQueue\PublisherInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\ImportExport\Controller\Adminhtml\Export\Export as ExportController;
use Magento\ImportExport\Model\Export as ExportModel;
use Magento\ImportExport\Model\Export\Entity\ExportInfoFactory;
use Magento\Backend\Model\View\Result\Redirect;
use Psr\Log\LoggerInterface;
use Exception;

class Export extends ExportController implements HttpPostActionInterface
{
    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * @var SessionManagerInterface
     */
    private $sessionManager;

    /**
     * @var PublisherInterface
     */
    private $messagePublisher;

    /**
     * @var ExportInfoFactory
     */
    private $exportInfoFactory;

    /**
     * @param Context $context
     * @param FileFactory $fileFactory
     * @param SessionManagerInterface|null $sessionManager
     * @param PublisherInterface|null $publisher
     * @param ExportInfoFactory|null $exportInfoFactory
     */
    public function __construct(
        Context $context,
        FileFactory $fileFactory,
        ?SessionManagerInterface $sessionManager = null,
        ?PublisherInterface $publisher = null,
        ?ExportInfoFactory $exportInfoFactory = null
    ) {
        parent::__construct($context, $fileFactory);

        $this->sessionManager = $sessionManager ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(SessionManagerInterface::class);
        $this->messagePublisher = $publisher ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(PublisherInterface::class);
        $this->exportInfoFactory = $exportInfoFactory ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(ExportInfoFactory::class);
    }

    /**
     * Plugin for Export execute function.
     *
     * @param ExportController $subject
     * @param callable $proceed
     * @return Redirect|void
     */
    public function aroundExecute(ExportController $subject, callable $proceed)
    {
        if ($subject->getRequest()->getPost(ExportModel::FILTER_ELEMENT_GROUP)) {
            try {
                $params = $subject->getRequest()->getParams();
                $params['skip_attr'] = $params['skip_attr'] ?? [];

                $model = $subject->_objectManager->create(ExportModel::class);
                $model->setData($params);
                $this->sessionManager->writeClose();

                return $this->fileFactory->create(
                    $model->getFileName(),
                    $model->export(),
                    \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR,
                    $model->getContentType()
                );

            } catch (Exception $e) {
                $subject->_objectManager->get(LoggerInterface::class)->critical($e);
                $subject->messageManager->addErrorMessage(__('Please correct the data sent value.'));
            }
        } else {
            $subject->messageManager->addErrorMessage(__('Please correct the data sent value.'));
        }

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('adminhtml/*/index');
        return $resultRedirect;
    }
}

