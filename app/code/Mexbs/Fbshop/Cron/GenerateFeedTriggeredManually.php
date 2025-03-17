<?php
namespace Mexbs\Fbshop\Cron;

class GenerateFeedTriggeredManually
{
    protected $helper;
    protected $scheduleStoreCollectionFactory;
    protected $storeManager;

    public function __construct(
        \Mexbs\Fbshop\Helper\Data $helper,
        \Mexbs\Fbshop\Model\ResourceModel\ScheduleStore\CollectionFactory $scheduleStoreCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ){
        $this->helper = $helper;
        $this->scheduleStoreCollectionFactory = $scheduleStoreCollectionFactory;
        $this->storeManager = $storeManager;
    }

    public function execute($schedule){
        $this->helper->removeProgressFile();

        /**
         * @var \Mexbs\Fbshop\Model\ResourceModel\ScheduleStore\Collection $scheduleStoreCollection
         */
        $scheduleStoreCollection = $this->scheduleStoreCollectionFactory->create();
        $scheduleStoreCollection->addFieldToFilter("schedule_id", $schedule->getId());
        if(!$scheduleStoreCollection->getFirstItem() || !$scheduleStoreCollection->getFirstItem()->getId()){
            throw new \Exception("There is no matching store ID for the cron job");
        }
        $storeId = $scheduleStoreCollection->getFirstItem()->getStoreId();
        if(!is_numeric($storeId)){
            throw new \Exception("Non numeric store ID provided to schedule");
        }

        $store = $this->storeManager->getStore($storeId);
        if(!$store || !$store->getCode()){
            throw new \Exception(sprintf("Store with ID %s doesn't exist", $storeId));
        }

        $this->helper->generateFeed("backend", $store);
    }
}