<?php

namespace Shiptheory\Shippingx\Model\ResourceModel;

class ShipmentHistory extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('shiptheory_shipment', 'id');
    }

    /**
     * Load data by specified shipment_id
     *
     * @param string $shipmentId
     * @return array
     */
    public function loadByShipmentId($shipmentId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from($this->getMainTable())->where('shipment_id=:shipment_id');
        $binds = ['shipment_id' => $shipmentId];
        return $connection->fetchRow($select, $binds);
    }
}
