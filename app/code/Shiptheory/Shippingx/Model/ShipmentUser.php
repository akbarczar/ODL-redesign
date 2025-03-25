<?php

namespace Shiptheory\Shippingx\Model;

use Magento\Framework\Model\AbstractModel;
use Shiptheory\Shippingx\Api\Data\ShipmentUserInterface;

class ShipmentUser extends AbstractModel  implements ShipmentUserInterface
{
    public function _construct()
    {
        $this->_init(\Shiptheory\Shippingx\Model\ResourceModel\ShipmentUser::class);
    }
}
