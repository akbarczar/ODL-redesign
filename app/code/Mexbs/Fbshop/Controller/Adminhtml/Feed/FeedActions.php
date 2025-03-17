<?php
namespace Mexbs\Fbshop\Controller\Adminhtml\Feed;


class FeedActions extends \Magento\Backend\App\Action
{
    protected $helper;
    public function __construct(
        \Mexbs\Fbshop\Helper\Data $helper,
        \Magento\Backend\App\Action\Context $context
    )
    {
        $this->helper = $helper;
        parent::__construct($context);
    }

    public function execute()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Mexbs_Fbshop::feed_actions');
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Feed Actions'));
        $this->_addContent(
            $this->_view->getLayout()->createBlock(\Mexbs\Fbshop\Block\Adminhtml\FeedActions::class)
    );
        $this->_view->renderLayout();
    }
}