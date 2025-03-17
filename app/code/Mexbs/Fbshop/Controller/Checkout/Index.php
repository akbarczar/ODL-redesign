<?php
namespace Mexbs\Fbshop\Controller\Checkout;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $resultJsonFactory;
    protected $cart;
    protected $helper;
    protected $productRepository;
    protected $storeManager;
    protected $fbProductUrl;
    protected $mappingCollectionFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Checkout\Model\Cart $cart,
        ProductRepositoryInterface $productRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Mexbs\Fbshop\Helper\Data $helper,
        \Mexbs\Fbshop\Model\ResourceModel\AttributesMapping\CollectionFactory $mappingCollectionFactory,
        \Mexbs\Fbshop\Model\Product\Url $fbProductUrl
    ){
        $this->resultJsonFactory = $resultJsonFactory;
        $this->cart = $cart;
        $this->helper = $helper;
        $this->productRepository = $productRepository;
        $this->storeManager = $storeManager;
        $this->mappingCollectionFactory = $mappingCollectionFactory;
        $this->fbProductUrl = $fbProductUrl;
        parent::__construct($context);
    }

    protected function _initProduct($productId, $storeId)
    {
        try {
            return $this->productRepository->getById($productId, false, $storeId);
        } catch (NoSuchEntityException $e) {
            return false;
        }
    }

    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $productId = $params['product_id'];

        $this->helper->setFbTrackingCookie();

        $storeId = (isset($params['store_id']) ? $params['store_id'] : null);
        try{
            if(!isset($storeId)){
                throw new \Exception("No store ID provided");
            }

            $product = $this->_initProduct($productId, $storeId);
            $productUrl = $this->fbProductUrl->getProductUrlForStore($product, $this->storeManager->getStore($storeId));

            if(strpos($this->_request->getServer('HTTP_REFERER'), "instagram") !== false){
                $this->_redirect($productUrl);
            }else{
                $productAddToCartParams = [
                    'product_id' => $productId,
                    'qty' => 1,
                    'super_attribute' => []
                ];

                foreach($params as $paramKey => $paramValue){
                    if(is_numeric($paramValue)){
                        $attributeCode = preg_replace("/[^a-zA-Z0-9\-_]/", "", $paramKey);
                        $attributeId = $this->helper->getProductAttributeIdByCode($attributeCode);
                        if($attributeId){
                            $productAddToCartParams['super_attribute'][$attributeId] = $paramValue;
                        }
                    }
                }

                $this->cart->addProduct($product, $productAddToCartParams);

                $this->_eventManager->dispatch(
                    'checkout_cart_add_product_complete',
                    ['product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse()]
                );
                $this->cart->save();

                $this->_redirect("checkout");
            }
        }catch(\Exception $e){
            if(isset($productUrl)){
                $this->_redirect($productUrl);
            }else{
                $this->_redirect("/");
            }
        }
    }
}
