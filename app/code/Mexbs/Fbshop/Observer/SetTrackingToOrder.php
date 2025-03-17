<?php
namespace Mexbs\Fbshop\Observer;

use Magento\Framework\Event\ObserverInterface;

class SetTrackingToOrder implements ObserverInterface
{
    protected $cookieManager;

    public function __construct(
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
    ) {
        $this->cookieManager = $cookieManager;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /* @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getData('order');

        if($this->cookieManager->getCookie(\Mexbs\Fbshop\Helper\Data::FB_COOKIE_NAME) == '1'){
            $order->setIsFromFb(1);
        }

        return $this;
    }
}
