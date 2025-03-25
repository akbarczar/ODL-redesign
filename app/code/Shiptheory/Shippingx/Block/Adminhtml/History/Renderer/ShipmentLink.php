<?php

namespace Shiptheory\Shippingx\Block\Adminhtml\History\Renderer;

use Magento\Framework\DataObject;

class ShipmentLink extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    protected $_orderShipment;
    protected $_urlBuider;

    public function __construct(
        \Magento\Sales\Model\Order\Shipment $orderShipment,
        \Magento\Framework\UrlInterface $urlBuilder
    ) {
        $this->_orderShipment = $orderShipment;
        $this->_urlBuilder = $urlBuilder;
    }

    public function render(DataObject $row)
    {
        $shipDetail = $this->_orderShipment->load($row->getShipmentId());
        $url = $this->_urlBuilder->getUrl(
            'sales/shipment/view',
            ['shipment_id' => $row->getShipmentId()]
        );
        $html = '<a href="' . $url . '" target="_blank">' . $shipDetail->getIncrementId() . '</a>';
        return $html;
    }
}
