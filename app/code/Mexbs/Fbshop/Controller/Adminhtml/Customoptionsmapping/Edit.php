<?php
namespace Mexbs\Fbshop\Controller\Adminhtml\Customoptionsmapping;

use Magento\Backend\App\Action;

class Edit extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Mexbs_Fbshop::product_custom_options_mapping';

    protected $_coreRegistry;
    protected $resultPageFactory;
    protected $mappingFactory;


    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Mexbs\Fbshop\Model\CustomOptionsMappingFactory $mappingFactory
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
        $resultPage->setActiveMenu('Mexbs_Fbshop::product_custom_options_mapping')
            ->addBreadcrumb(__('Facebook Shop Integration'), __('Facebook Shop Integration'))
            ->addBreadcrumb(__('Product Custom Options Mapping'), __('Product Custom Options Mapping'));
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
                $this->messageManager->addError(__('This custom option mapping no longer exists.'));
                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        $this->_coreRegistry->register('current_mapping', $model);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Custom Option Mapping') : __('New Custom Option Mapping'),
            $id ? __('Edit Custom Option Mapping') : __('New Custom Option Mapping')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Custom Options Mappings'));
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getId() ? __('Edit Custom Option Mapping') : __('New Custom Option Mapping'));

        return $resultPage;
    }
}
