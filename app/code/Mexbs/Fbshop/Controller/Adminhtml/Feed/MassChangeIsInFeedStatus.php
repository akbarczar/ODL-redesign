<?php
namespace Mexbs\Fbshop\Controller\Adminhtml\Feed;

use Magento\Framework\Controller\ResultFactory;

class MassChangeIsInFeedStatus extends \Magento\Backend\App\Action
{
    const STATUS_IN_FEED = 1;
    const STATUS_NOT_IN_FEED = 1;

    protected $productFactory;
    protected $helper;
    protected $productResource;
    protected $filter;
    protected $productCollectionFactory;
    protected $productAction;
    protected $storeManager;


    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Mexbs\Fbshop\Helper\Data $helper,
        \Magento\Catalog\Model\ResourceModel\Product $productResource,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Action $productAction,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ){
        $this->productFactory = $productFactory;
        $this->helper = $helper;
        $this->productResource = $productResource;
        $this->filter = $filter;
        $this->productAction = $productAction;
        $this->storeManager = $storeManager;
        $this->productCollectionFactory = $productCollectionFactory;

        parent::__construct($context);
    }


    protected  function changeProductsInFeed($productIds, $isInFeed){
        foreach($this->storeManager->getStores(true) as $store){
            $this->productAction->updateAttributes($productIds, ['is_in_fb_feed' => $isInFeed], $store->getId());
        }
    }

    protected function markProductsInFeed($productIds){
        $this->changeProductsInFeed($productIds, \Magento\Eav\Model\Entity\Attribute\Source\Boolean::VALUE_YES);
    }

    protected function markProductsNotInFeed($productIds){
        $this->changeProductsInFeed($productIds, \Magento\Eav\Model\Entity\Attribute\Source\Boolean::VALUE_NO);
    }


    public function execute()
    {
        $collection = $this->filter->getCollection($this->productCollectionFactory->create());
        $productIds = $collection->getAllIds();

        $isInFeed = $this->getRequest()->getParam('is_in_feed');

        if (!$productIds || !is_array($productIds)) {
            throw new \Exception(__('No products selected for update.'));
        }

        if($isInFeed == self::STATUS_IN_FEED){
            $this->markProductsInFeed($productIds);
        }else{
            $this->markProductsNotInFeed($productIds);
        }

        $this->messageManager->addSuccessMessage("The Facebook Feed status were updated for the selected products.");
        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('catalog/product/index');
    }
}