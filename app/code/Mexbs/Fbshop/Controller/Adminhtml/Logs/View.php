<?php
namespace Mexbs\Fbshop\Controller\Adminhtml\Logs;

class View extends \Magento\Backend\App\Action
{
    protected $feedLogFactory;
    protected $coreRegistry;

    public function __construct(
        \Mexbs\Fbshop\Model\LogFactory $feedLogFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\App\Action\Context $context
    )
    {
        $this->coreRegistry = $coreRegistry;
        $this->feedLogFactory = $feedLogFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $feedLogId = $this->getRequest()->getParam('id');
        $feedLog = $this->feedLogFactory->create()->load($feedLogId);

        $this->coreRegistry->register(\Mexbs\Fbshop\Helper\Data::CURRENT_FEED_LOG, $feedLog);

        $this->_view->loadLayout();
        $this->_setActiveMenu('Mexbs_Fbshop::update_logs');
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('View Log'));
        $this->_addContent(
            $this->_view->getLayout()->createBlock(\Mexbs\Fbshop\Block\Adminhtml\ViewLog::class)
    );
        $this->_view->renderLayout();
    }
}