<?php
namespace Mexbs\Fbshop\Controller\Adminhtml\Attributesmapping;

class Save extends \Magento\Backend\App\Action
{
    protected $resultRawFactory;
    protected $resultJsonFactory;
    protected $layoutFactory;
    protected $attributesMappingFactory;
    protected $logger;

    protected function _initMapping()
    {
        $mappingId = (int)$this->getRequest()->getParam('mapping_id', false);
        $mapping = $this->attributesMappingFactory->create();
        if ($mappingId) {
            $mapping->load($mappingId);
        }
        return $mapping;
    }

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Mexbs\Fbshop\Model\AttributesMappingFactory $attributesMappingFactory,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->resultRawFactory = $resultRawFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->layoutFactory = $layoutFactory;
        $this->attributesMappingFactory = $attributesMappingFactory;
        $this->logger = $logger;
    }


    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $mapping = $this->_initMapping();
        if (!$mapping) {
            return $resultRedirect->setPath('*/*/', ['_current' => true, 'id' => null]);
        }

        $postData = $this->getRequest()->getPostValue();
        if(empty($postData['mapping_id'])){
            $postData['mapping_id'] = null;
        }

        if ($postData) {
            $mapping->addData($postData);

            try {
                $mapping->save();
                $this->messageManager->addSuccessMessage(__('You saved the mapping.'));
            } catch (\Magento\Framework\Exception\AlreadyExistsException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->logger->critical($e);
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->logger->critical($e);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('Something went wrong while saving the mapping.'));
                $this->logger->critical($e);
            }
        }


        if ($this->getRequest()->getParam('back')) {
            $this->_redirect('*/*/edit', ['id' => $mapping->getId()]);
            return;
        }
        $this->_redirect('*/*/index');
    }
}
