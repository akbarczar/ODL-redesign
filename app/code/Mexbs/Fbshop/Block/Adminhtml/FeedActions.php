<?php
namespace Mexbs\Fbshop\Block\Adminhtml;

class FeedActions extends \Magento\Backend\Block\Template
{
    protected $_template = 'Mexbs_Fbshop::feed_actions.phtml';
    protected $helper;
    protected $currentFeedId;
    protected $fetchedCurrentFeedId = false;
    protected $context;

    public function __construct(
        \Mexbs\Fbshop\Helper\Data $helper,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    )
    {
        $this->helper = $helper;
        $this->context = $context;
        parent::__construct($context, $data);
    }

    protected function _prepareLayout()
    {
            $this->getToolbar()->addChild(
                'generate_button',
                \Magento\Backend\Block\Widget\Button::class,
            [
                'label' => __('Schedule Feed Generation Now'),
                'class' => 'save primary generate-feed'
            ]
        );

        return parent::_prepareLayout();
    }

    public function getHeader()
    {
        return __('Feed Actions');
    }


    public function getGenerateButtonHtml()
    {
        return $this->getChildHtml('generate_button');
    }

    public function getCurrentFeedId(){
        if(!$this->fetchedCurrentFeedId){
            try{
                $this->currentFeedId = $this->helper->getFeedId($this->context->getStoreManager()->getStore());
            }catch(\Exception $e){

            }
            $this->fetchedCurrentFeedId = true;
        }
        return $this->currentFeedId;
    }


    public function getProgressFileUrl(){
        return $this->helper->getProgressFileUrl();
    }


    public function getIsFeedFileExists($storeCode){
        return $this->helper->getIsFeedFileExists($storeCode);
    }

    public function getFeedFileUrl($store){
        return $this->helper->getFeedFileUrl($store->getCode());
    }

    public function getMissingFieldsErrorMessage($includeHrefs = false){
        return $this->helper->getMissingFieldsErrorMessage($includeHrefs);
    }

    public function getFeedId($store){
        return $this->helper->getFeedId($store);
    }

    public function getIsAppendFeedId($store){
        return $this->helper->getIsAppendFeedIdToProductId($store);
    }

    public function getStores(){
        return $this->context->getStoreManager()->getStores();
    }
}