<?php

namespace Shiptheory\Shippingx\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class ShipmentUser extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('shiptheory_shipment_user', 'id');
    }
}
