<?php

namespace Shiptheory\Shippingx\Controller\Adminhtml\Sales\Order;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Shiptheory\Shippingx\Model\ShipOrderFactory;

class MassShipOrder extends \Magento\Sales\Controller\Adminhtml\Order\AbstractMassAction
{
    protected $_shipOrder;
    protected $redirectUrl = 'sales/order/index';

    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        ShipOrderFactory $shipOrder
    ) {
        parent::__construct($context, $filter);
        $this->collectionFactory = $collectionFactory;
        $this->_shipOrder = $shipOrder;
    }

    /**
     * Ship selected orders
     *
     * @param AbstractCollection $collection
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    protected function massAction(AbstractCollection $collection)
    {
        $countShipOrder = 0;
        foreach ($collection->getItems() as $order) {
            try {
                $shipOrder = $this->_shipOrder->create();
                $ship = $shipOrder->shipOrder($order->getId());
                $countShipOrder++;
            } catch (\Exception $e) {
                $this->messageManager->addError(__('Order Shipments Already Created For : %1', $order['increment_id']));
            }
        }
        $countNonShipOrder = $collection->count() - $countShipOrder;
        if ($countShipOrder) {
            $string = $countShipOrder . ' success, ' . $countNonShipOrder . ' fails';
            $this->messageManager->addSuccess(__('Order shipments processed : %1.', $string));
        }
        $this->_redirect('sales/order/index');
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Shiptheory_Shippingx::shiptheory');
    }
}
