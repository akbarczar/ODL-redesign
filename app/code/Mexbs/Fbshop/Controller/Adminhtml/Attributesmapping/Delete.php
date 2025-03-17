<?php
namespace Mexbs\Fbshop\Controller\Adminhtml\Attributesmapping;

use Magento\Backend\App\Action;

class Delete extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Mexbs_Fbshop::product_attribute_mapping';

    protected $mappingFactory;
    protected $logger;


    public function __construct(
        Action\Context $context,
        \Mexbs\Fbshop\Model\AttributesMappingFactory $mappingFactory,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->mappingFactory = $mappingFactory;
        $this->logger = $logger;
        parent::__construct($context);
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                $mapping = $this->mappingFactory->create()->load($id);
                $mapping->delete();
                $this->messageManager->addSuccessMessage(__('You deleted the mapping.'));
                $this->_redirect('*/*/index');
                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->logger->critical($e);
                $this->_redirect('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
                return;
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('We can\'t delete the mapping right now. Please review the log and try again.')
                );
                $this->logger->critical($e);
                $this->_redirect('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
                return;
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a mapping to delete.'));
        $this->_redirect('*/*/index');
    }
}
