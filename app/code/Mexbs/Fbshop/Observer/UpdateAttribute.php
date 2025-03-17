<?php
namespace Mexbs\Fbshop\Observer;

use Magento\Framework\Event\ObserverInterface;

class UpdateAttribute implements ObserverInterface{
    protected $fbHelper;
    protected $productCollectionFactory;

    public function __construct(
        \Mexbs\Fbshop\Helper\Data $fbHelper,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
    ){
        $this->fbHelper = $fbHelper;
        $this->productCollectionFactory = $productCollectionFactory;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $attrData = $observer->getEvent()->getAttributesData();
        $productIds = $observer->getEvent()->getProductIds();
        $storeId = $observer->getEvent()->getStoreId();

        if(!is_array($attrData) || !is_array($productIds) || (!$storeId && ($storeId !== 0))){
            return;
        }

        if((isset($attrData['is_in_fb_feed'])
                && ($attrData['is_in_fb_feed'] == 1))
            || (isset($attrData['is_resize_main_image_for_fb'])
                && ($attrData['is_resize_main_image_for_fb'] == 1))
        ){
            /**
             * @var \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection
             */
            $productCollection = $this->productCollectionFactory->create();



            $productCollection->addFieldToFilter("entity_id", ['in' => $productIds])
                ->addAttributeToSelect("image");

            foreach($productCollection as $product){
                if((isset($attrData['is_resize_main_image_for_fb'])
                        && ($attrData['is_resize_main_image_for_fb'] == 1))
                    || $product->getIsResizeMainImageForFb()){
                    $baseImage = $product->getImage();

                    if($baseImage
                        && preg_match("/^(.*)\/(.*)$/",$baseImage,$matches)) {
                        if(count($matches) > 2){
                            $subPath = $matches[1];
                            $fileName = $matches[2];

                            if(strpos($fileName, ".") !== false){
                                $this->fbHelper->recreateFbImage($subPath, $fileName);
                            }
                        }
                    }
                }
            }
        }
    }
}