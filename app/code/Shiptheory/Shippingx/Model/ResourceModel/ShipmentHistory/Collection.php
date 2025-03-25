<?php

namespace Shiptheory\Shippingx\Model\ResourceModel\ShipmentHistory;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            \Shiptheory\Shippingx\Model\ShipmentHistory::class,
            \Shiptheory\Shippingx\Model\ResourceModel\ShipmentHistory::class
        );
    }
}
