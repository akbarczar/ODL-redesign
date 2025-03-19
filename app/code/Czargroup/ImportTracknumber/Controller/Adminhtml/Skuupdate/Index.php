<?php

namespace Czargroup\ImportTracknumber\Controller\Adminhtml\Skuupdate;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\File\Csv;
use Magento\Catalog\Model\ProductRepository;
use Psr\Log\LoggerInterface;

class Index extends Action
{
    protected $_adminResource = 'Czargroup_ImportTracknumber::skuupdate';

    protected $csv;
    protected $productRepository;
    protected $logger;

    public function __construct(
        Action\Context $context,
        Csv $csv,
        ProductRepository $productRepository,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->csv = $csv;
        $this->productRepository = $productRepository;
        $this->logger = $logger;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Czargroup_ImportTracknumber::skuupdate');
    }

    public function execute()
    {
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/Skuupdate.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        $logger->info('controller called');

        // Debug request method and files
        $logger->info('Request Method: ' . $this->getRequest()->getMethod());
        $logger->info('Files: ' . print_r($_FILES, true));

        if ($this->getRequest()->isPost() && isset($_FILES['sku_file']['tmp_name'])) {
            $logger->info('controller if is post checked');
            try {
                $logger->info('inside try');
                $file = $_FILES['sku_file']['tmp_name'];
                $data = $this->csv->getData($file);

                if (count($data) > 0) {
                    $updated = 0;
                    $failed = 0;

                    foreach ($data as $row) {
                        if (count($row) < 2) {
                            continue;
                        }
                    
                        [$oldSku, $newSku] = $row;
                    
                        try {
                            $product = $this->productRepository->get($oldSku);
                    
                            if ($product->getSku() === $newSku) {
                                // If the SKU is already updated, show a specific message
                                $this->messageManager->addNoticeMessage(__('SKU "%1" is already updated.', $oldSku));
                                continue;
                            }
                    
                            $product->setSku($newSku);
                            $this->productRepository->save($product);
                            $updated++;
                        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                            $this->messageManager->addErrorMessage(__('SKU "%1" not found.', $oldSku));
                            $failed++;
                        } catch (\Exception $e) {
                            $this->logger->error(__('SKU Update Failed: %1', $e->getMessage()));
                            $failed++;
                        }
                    }

                    $this->messageManager->addSuccessMessage(__('Updated %1 SKUs. Failed: %2', $updated, $failed));
                } else {
                    $this->messageManager->addErrorMessage(__('Invalid CSV file.'));
                }
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('Error: %1', $e->getMessage()));
            }

            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            return $resultRedirect->setPath('*/*/index');
        }

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->prepend(__('SKU Update'));
        return $resultPage;
    }
}
