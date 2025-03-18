<?php

namespace Czargroup\Refund\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Sales\Model\ResourceModel\Order as OrderResource;

class UpdateOrderGrid implements ObserverInterface
{
    protected $orderResource;

    public function __construct(
        OrderResource $orderResource
    ) {
        $this->orderResource = $orderResource;
    }

    public function execute(Observer $observer)
    {
        $creditmemo = $observer->getEvent()->getCreditmemo();
        $order = $creditmemo->getOrder();
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/Refund.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        $logger->info('text message');
        $logger->info(print_r($creditmemo->getRefundReasons(), true));

        if ($creditmemo->getRefundReasons()) {
            $order->setData('refund_reasons', $creditmemo->getRefundReasons());
            $this->orderResource->save($order);
        }
    }
}
