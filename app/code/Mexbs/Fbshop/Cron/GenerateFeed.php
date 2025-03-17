<?php
namespace Mexbs\Fbshop\Cron;

class GenerateFeed
{
    protected $helper;
    protected $storeManager;

    public function __construct(
        \Mexbs\Fbshop\Helper\Data $helper,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ){
        $this->helper = $helper;
        $this->storeManager = $storeManager;
    }

    public function execute(){
        $this->helper->removeProgressFile();

        foreach($this->storeManager->getStores() as $store){
            if($this->helper->getIsFeedGenerationScheduleEnabled($store)){
                $this->helper->generateFeed("cron", $store);
            }
        }
    }
}