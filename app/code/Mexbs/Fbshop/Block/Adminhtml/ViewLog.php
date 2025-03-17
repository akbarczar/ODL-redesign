<?php
namespace Mexbs\Fbshop\Block\Adminhtml;

class ViewLog extends \Magento\Backend\Block\Template
{
    protected $_template = 'Mexbs_Fbshop::view_log.phtml';
    protected $coreRegistry;
    protected $helper;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Mexbs\Fbshop\Helper\Data $helper,
        array $data = []
    )
    {
        $this->coreRegistry = $coreRegistry;
        $this->helper = $helper;
        parent::__construct($context, $data);
    }


    public function getHeader()
    {
        return __('View Log');
    }

    public function getCurrentFeedLog(){
        return $this->coreRegistry->registry(\Mexbs\Fbshop\Helper\Data::CURRENT_FEED_LOG);
    }

    public function getDateFromTimestamp($timestamp){
        return $this->helper->getDateFromTimestamp($timestamp);
    }
}