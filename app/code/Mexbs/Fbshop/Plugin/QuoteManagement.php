<?php
namespace Mexbs\Fbshop\Plugin;

use Magento\Quote\Model\Quote as QuoteEntity;

class QuoteManagement
{
    protected $cookieManager;

    public function __construct(
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager

    ) {
        $this->cookieManager = $cookieManager;
    }

    public function beforeSubmit(
        \Magento\Quote\Model\QuoteManagement $subject,
        QuoteEntity $quote, $orderData = []
    ){
        if($this->cookieManager->getCookie(\Mexbs\Fbshop\Helper\Data::FB_COOKIE_NAME) == '1'){
            $quote->setIsFromFb(1);
        }
        return [$quote, $orderData];
    }
}
