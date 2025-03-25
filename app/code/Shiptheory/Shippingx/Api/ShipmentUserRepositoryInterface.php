<?php
namespace Shiptheory\Shippingx\Api;

interface ShipmentUserRepositoryInterface
{
    /**
     * @param $shipment_id
     * @return mixed
     */
    public function getByShipmentId($shipment_id);

    /**
     * @param Data\ShipmentUserInterface $shipmentUser
     * @return mixed
     */
    public function save(Data\ShipmentUserInterface $shipmentUser);
}
