<?php

namespace Shiptheory\Shippingx\Block\Adminhtml\History\Renderer;

use Magento\Framework\DataObject;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\Order\ShipmentRepository;
use Shiptheory\Shippingx\Api\ShipmentUserRepositoryInterface;
use Shiptheory\Shippingx\Api\UserRepositoryInterface;


class User extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var ShipmentUserRepositoryInterface
     */
    private ShipmentUserRepositoryInterface $shipmentUserRepository;

    /**
     * @var UserRepositoryInterface
     */
    private UserRepositoryInterface $userRepository;

    /**
     * User constructor.
     * @param ShipmentUserRepositoryInterface $shipmentUserRepository
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(
        ShipmentUserRepositoryInterface $shipmentUserRepository,
        UserRepositoryInterface $userRepository
    ) {
        $this->shipmentUserRepository = $shipmentUserRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @param DataObject $row
     * @return string
     */
    public function render(DataObject $row)
    {
        try {
            $shipment_user = $this->shipmentUserRepository->getByShipmentId($row->getShipmentId());
            $user = $this->userRepository->getById($shipment_user->getUserId());

        } catch (NoSuchEntityException $exception){
            return "User deleted or not assigned";
        } catch (InputException $exception) {
            return "No shipment ID";
        }

        return $user->getName();
    }
}
