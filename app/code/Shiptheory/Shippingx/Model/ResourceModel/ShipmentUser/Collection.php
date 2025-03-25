<?php

namespace Shiptheory\Shippingx\Model\ResourceModel\ShipmentUser;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            \Shiptheory\Shippingx\Model\ShipmentUser::class,
            \Shiptheory\Shippingx\Model\ResourceModel\ShipmentUser::class
        );
    }
}
