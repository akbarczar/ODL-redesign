<?php

namespace Shiptheory\Shippingx\Model;

class ShipOrder extends \Magento\Framework\Model\AbstractModel
{
    protected $_scopeConfig;
    protected $_order;
    protected $_orderConverter;
    protected $eventManager;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Sales\Model\OrderFactory $order,
        \Magento\Sales\Model\Convert\OrderFactory $orderConverter,
        \Magento\Framework\Event\Manager $eventManager
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_order = $order;
        $this->eventManager = $eventManager;
        $this->_orderConverter = $orderConverter;
    }

    public function shipOrder($orderId)
    {
        //load by order
        $order = $this->_order->create()->load($orderId);
        // Check if order can be shipped or has already shipped
        if (!$order->canShip()) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('You can\'t create an shipment for order increment id: %1', $order->getIncrementId())
            );
        } else {
            $shipment = $this->createShipment($order);
            return $shipment;
        }
    }

    public function createShipment($order)
    {
        // Initialize the order shipment object
        $convertOrder = $this->_orderConverter->create();
        $shipment = $convertOrder->toShipment($order);
        // Loop through order items
        foreach ($order->getAllItems() as $orderItem) {
            // Check if order item has qty to ship or is virtual
            if (!$orderItem->getQtyToShip() || $orderItem->getIsVirtual()) {
                continue;
            }
            $qtyShipped = $orderItem->getQtyToShip();
            // Create shipment item with qty
            $shipmentItem = $convertOrder->itemToShipmentItem($orderItem)->setQty($qtyShipped);
            // Add shipment item to shipment
            $shipment->addItem($shipmentItem);
        }
        // Register shipment
        $shipment->register();
        $shipment->getOrder()->setIsInProcess(true);
        try {
            // Save created shipment and order
            $shipment->save();
            $shipment->getOrder()->save();
            return $shipment;
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()));
        }
    }
}
