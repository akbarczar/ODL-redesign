<?php
namespace Mexbs\Fbshop\Controller\Adminhtml\Attributesmapping;

use Magento\Backend\App\Action;

class Edit extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Mexbs_Fbshop::product_attribute_mapping';

    protected $_coreRegistry;
    protected $resultPageFactory;
    protected $mappingFactory;


    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Mexbs\Fbshop\Model\AttributesMappingFactory $mappingFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->mappingFactory = $mappingFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Mexbs_Fbshop::product_attribute_mapping')
            ->addBreadcrumb(__('Facebook Shop Integration'), __('Facebook Shop Integration'))
            ->addBreadcrumb(__('Product Attributes Mapping'), __('Product Attributes Mapping'));
        return $resultPage;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->mappingFactory->create();

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This mapping no longer exists.'));
                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        $this->_coreRegistry->register('current_mapping', $model);

        // 5. Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Mapping') : __('New Mapping'),
            $id ? __('Edit Mapping') : __('New Mapping')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Mappings'));
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getId() ? __('Edit Mapping') : __('New Mapping'));

        return $resultPage;
    }
}
