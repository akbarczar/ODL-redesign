<?php
/*
 * @copyright Deploy eCommerce 2022. All rights reserved.
 * @author Nathan Chick
 */

namespace Shiptheory\Shippingx\Plugin;

use Magento\Framework\Model\AbstractModel;
use Shiptheory\Shippingx\Model\ResourceModel\ShipmentHistory\Collection;

class ShipmentHistoryUser
{

    /**
     * @param Collection $subject
     * @param Collection $result
     * @return Collection
     */
    public function afterGetCollection(\Shiptheory\Shippingx\Model\ShipmentHistory $subject,
                                       Collection $result): Collection
    {
        $result->getSelect()->joinLeft([$result->getTable('shiptheory_shipment_user')],
            'main_table.shipment_id = shiptheory_shipment_user.shipment_id',
            ['user_id']);

        return $result;
    }
}
