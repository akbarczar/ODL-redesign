<?php

namespace Shiptheory\Shippingx\Model;

class SubmitShipment extends \Magento\Framework\Model\AbstractModel
{
    protected $_scopeConfig;
    protected $_order;
    protected $_shipmentHistory;
    protected $_dateTime;
    protected $_messageManager;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Sales\Model\OrderFactory $order,
        \Shiptheory\Shippingx\Model\ShipmentHistoryFactory $shipmentHistoryFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Model\Context $context
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_order = $order;
        $this->_shipmentHistory = $shipmentHistoryFactory;
        $this->_dateTime = $dateTime;
        $this->_messageManager = $messageManager;
    }

    public function submitShipment($shipment)
    {
        $shipmentHistory = $this->_shipmentHistory->create();
        $shipmentExist = $shipmentHistory->loadByShipmentId($shipment->getId());
        if ($shipmentExist->getData()) {
            $shipmentHistory->load($shipmentExist->getId())->delete();
        }
        $response = $this->webHook($shipment);
        if ($response == true) {
            $success = true;
            $message = 'Request has been sent successsfully';
        } else {
            $success = false;
            $message = 'Request has been  not sent.';
        }
        $result = $this->storeShipmentHistory($shipment->getId(), $success, $message);
        return $result;
    }

    public function webHook($shipment)
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $apiKey = $this->_scopeConfig->getValue("shiptheory/setting/api_key", $storeScope, $shipment->getStoreId());
        $baseUrl = $this->_scopeConfig->getValue("web/secure/base_url", $storeScope, $shipment->getStoreId());
        $url = $this->formatUrl($baseUrl);
        $shipmentId = $shipment->getId();
        $shipmentIncrementId = $shipment->getIncrementId();
        $apiUrl = 'https://quark.madcapsule.com/shiptheory.py?channel=' . $apiKey;
        $apiUrl .= '/Magento2/' . $url . '/' . $shipmentIncrementId . '/shipment/created/' . $shipmentId;
        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);    // we want headers
        curl_setopt($ch, CURLOPT_NOBODY, true);    // we don't need body
        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($httpcode == 200) {
            return true;
        } else {
            $this->_messageManager->addError(__('Your requested not submitted:error response code- %1', $httpcode));
            return false;
        }
    }

    public function storeShipmentHistory($shipmentId, bool $success, $message)
    {
        $shipmentHistory = $this->_shipmentHistory->create();
        $data = [
            'shipment_id' => $shipmentId,
            'sucess' => $success,
            'message' => $message,
            'created_at' => $this->_dateTime->gmtDate()
        ];
        $shipmentHistory->setData($data);
        try {
            $shipmentHistory->save();
            return true;
        } catch (Exception $e) {
            $this->_messageManager->addError($e->getMessage());
        }
        return false;
    }

    /**
     * Returned formatted base url
     *
     *
     */
    public function formatUrl($url)
    {
        $mixed = parse_url($url);
        return $mixed['host'];
    }
}
