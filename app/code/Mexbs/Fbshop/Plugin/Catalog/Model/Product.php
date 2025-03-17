<?php
namespace Mexbs\Fbshop\Plugin\Catalog\Model;

class Product
{
    protected $fbHelper;
    protected $request;

    public function __construct(
        \Mexbs\Fbshop\Helper\Data $fbHelper,
        \Magento\Framework\App\RequestInterface $request
    ){
        $this->fbHelper = $fbHelper;
        $this->request = $request;
    }

    public function aroundSave(
        \Magento\Catalog\Model\Product $subject,
        \Closure $proceed
    ){
        $product = $subject;

        $requestData = $this->request->getPostValue();
        $newBaseImageTmp = (isset($requestData['product']['image']) ? $requestData['product']['image'] : null);

        $shouldRecreateFbImage = false;
        if($product->dataHasChangedFor('is_in_fb_feed')
            ||
            ($newBaseImageTmp
            && ($newBaseImageTmp != "no_selection")
            && $product->getIsInFbFeed()
            && $product->getIsResizeMainImageForFb())){
            $shouldRecreateFbImage = true;
        }

        $product = $proceed();

        if($shouldRecreateFbImage){
            $newBaseImage = $product->getImage();
            if(preg_match("/^(.*)\/(.*)$/",$newBaseImage,$matches)) {
                if(count($matches) > 2){
                    $subPath = $matches[1];
                    $fileName = $matches[2];

                    if(strpos($fileName, ".") !== false){
                        $this->fbHelper->recreateFbImage($subPath, $fileName);
                    }
                }
            }

        }

        return $product;
    }
}