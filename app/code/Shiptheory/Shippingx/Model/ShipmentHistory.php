<?php

namespace Shiptheory\Shippingx\Model;

class ShipmentHistory extends \Magento\Framework\Model\AbstractModel
{
    public function _construct()
    {
        $this->_init(\Shiptheory\Shippingx\Model\ResourceModel\ShipmentHistory::class);
    }

    /**
     * @param $shipmentId
     * @return $this
     */
    public function loadByShipmentId($shipmentId)
    {
        $data = $this->getResource()->loadByShipmentId($shipmentId);
        if ($data !== false) {
            $this->setData($data);
            $this->setOrigData();
        }
        return $this;
    }
}
