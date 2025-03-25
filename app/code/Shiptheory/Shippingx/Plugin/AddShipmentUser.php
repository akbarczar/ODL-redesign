<?php

namespace Shiptheory\Shippingx\Plugin;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Api\ShipmentRepositoryInterface;
use Shiptheory\Shippingx\Api\ShipmentUserRepositoryInterface;

class AddShipmentUser
{
    /**
     * @var ShipmentUserRepositoryInterface
     */
    private ShipmentUserRepositoryInterface $shipmentUserRepository;

    /**
     * AddShipmentUser constructor.
     * @param ShipmentUserRepositoryInterface $shipmentUserRepository
     */
    public function __construct(
        ShipmentUserRepositoryInterface $shipmentUserRepository
    ) {
        $this->shipmentUserRepository = $shipmentUserRepository;
    }

    /**
     * @param ShipmentRepositoryInterface $subject
     * @param ShipmentInterface $shipment
     * @param $id
     * @return ShipmentInterface
     */
    public function afterGet(
        ShipmentRepositoryInterface $subject,
        ShipmentInterface $shipment
    ) {
        if ($shipment->getShipmentUserId() == null) {
            if ($shipmentUser = $this->shipmentUserRepository->getByShipmentId($shipment->getId())) {
                $extensionAttributes = $shipment->getExtensionAttributes();
                $extensionAttributes->setShipmentUser($shipmentUser->getUserId());
                $shipment->setExtensionAttributes($extensionAttributes);
            }
        }

        return $shipment;
    }

    /**
     * @param ShipmentRepositoryInterface $subject
     * @param $result
     * @param SearchCriteriaInterface $searchCriteria
     * @return mixed
     */
    public function afterGetList(
        ShipmentRepositoryInterface $subject,
        $result,
        SearchCriteriaInterface $searchCriteria
    ) {
        if(count($result) > 0) {
            foreach ($result as $shipment) {
                if ($shipment->getShipmentUserId() == null) {
                    if ($shipmentUser = $this->shipmentUserRepository->getByShipmentId($shipment->getId())) {
                        $extensionAttributes = $shipment->getExtensionAttributes();
                        $extensionAttributes->setShipmentUser($shipmentUser->getUserId());
                        $shipment->setExtensionAttributes($extensionAttributes);
                    }
                }
            }
        }
        return $result;
    }
}
