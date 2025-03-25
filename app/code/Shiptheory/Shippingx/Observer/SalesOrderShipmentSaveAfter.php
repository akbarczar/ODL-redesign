<?php

namespace Shiptheory\Shippingx\Observer;

use Magento\Backend\Model\Auth\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\ScopeInterface;
use Shiptheory\Shippingx\Model\SubmitShipmentFactory;
use Shiptheory\Shippingx\Api\ShipmentUserRepositoryInterface;
use Shiptheory\Shippingx\Model\ShipmentUserFactory;

class SalesOrderShipmentSaveAfter implements ObserverInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var SubmitShipmentFactory
     */
    private $submitShipment;

    /**
     * @var ShipmentUserRepositoryInterface
     */
    private $shipmentUserRepository;

    /**
     * @var ShipmentUserFactory
     */
    private $shipmentUserFactory;

    /**
     * @var Session
     */
    private $session;

    /**
     * SalesOrderShipmentSaveAfter constructor.
     * @param SubmitShipmentFactory $submitShipment
     * @param ShipmentUserRepositoryInterface $shipmentUserRepository
     * @param ShipmentUserFactory $shipmentUserFactory
     * @param Session $session
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        SubmitShipmentFactory $submitShipment,
        ShipmentUserRepositoryInterface $shipmentUserRepository,
        ShipmentUserFactory $shipmentUserFactory,
        Session $session,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->submitShipment = $submitShipment;
        $this->shipmentUserRepository = $shipmentUserRepository;
        $this->shipmentUserFactory = $shipmentUserFactory;
        $this->session = $session;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param Observer $observer
     * @return $this|void
     */
    public function execute(Observer $observer)
    {
        $storeScope = ScopeInterface::SCOPE_STORES;
        $shipment = $observer->getEvent()->getShipment();
        $webhook = $this->scopeConfig->getValue("shiptheory/setting/webhook", $storeScope, $shipment->getStoreId());

        if ($webhook == true) {
            $this->submitShipment->create()->submitShipment($shipment);

            if ($user = $this->session->getUser()) {

                $shipmentUser = $this->shipmentUserFactory->create();
                $shipmentUser->setData([
                    'shipment_id' => $shipment->getId(),
                    'user_id' => $user->getId()
                ]);
                $this->shipmentUserRepository->save($shipmentUser);
            }
        }

        return $this;
    }
}
