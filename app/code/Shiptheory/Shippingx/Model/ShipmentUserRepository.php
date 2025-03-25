<?php

namespace Shiptheory\Shippingx\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Shiptheory\Shippingx\Model\ShipmentUserFactory;
use Shiptheory\Shippingx\Api\Data\ShipmentUserInterface;
use Shiptheory\Shippingx\Api\ShipmentUserRepositoryInterface;

/**
 * Class ShipmentUser
 * @package Shiptheory\Shippingx\Model
 */
class ShipmentUserRepository implements ShipmentUserRepositoryInterface
{
    /**
     * @var ResourceModel\ShipmentUser
     */
    private ResourceModel\ShipmentUser $resourceShipmentUser;

    /**
     * @var ShipmentUserFactory
     */
    private ShipmentUserFactory $shipmentUserFactory;

    /**
     * ShipmentUserRepository constructor.
     * @param ResourceModel\ShipmentUser $resourceShipmentUser
     * @param ShipmentUserFactory $shipmentUserFactory
     */
    public function __construct(
        ResourceModel\ShipmentUser $resourceShipmentUser,
        ShipmentUserFactory $shipmentUserFactory
    ){
        $this->resourceShipmentUser = $resourceShipmentUser;
        $this->shipmentUserFactory = $shipmentUserFactory;
    }

    /**
     * @param $shipment_id
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getByShipmentId($shipment_id)
    {
        /* @var \Shiptheory\Shippingx\Api\Data\ShipmentUserInterface $shipmentUser */
        $shipmentUser = $this->shipmentUserFactory->create();

        $this->resourceShipmentUser->load($shipmentUser, $shipment_id, 'shipment_id');

        if (!$shipmentUser->getId()) {
            throw new NoSuchEntityException(__('Shipment User does not exist for shipment %1', $shipment_id));
        }

        return $shipmentUser;
    }

    /**
     * @param ShipmentUserInterface $shipmentUser
     * @return ShipmentUserInterface
     */
    public function save(ShipmentUserInterface $shipmentUser)
    {
        try {
            $this->resourceShipmentUser->save($shipmentUser);
        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }

        return $shipmentUser;
    }
}
