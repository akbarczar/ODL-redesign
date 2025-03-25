<?php

namespace Shiptheory\Shippingx\Controller\Adminhtml\History;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class MassSubmitShipTheory extends \Magento\Backend\App\Action
{
    protected $resultPageFactory;
    protected $_shipmentFactory;
    protected $_api;
    protected $_submitShipment;

    public function __construct(
        Context $context,
        \Magento\Sales\Model\Order\Shipment $shipmentFactory,
        PageFactory $resultPageFactory,
        \Shiptheory\Shippingx\Model\ApiFactory $api,
        \Shiptheory\Shippingx\Model\SubmitShipmentFactory $submitShipment
    ) {
        parent::__construct($context);
        $this->_api = $api;
        $this->_submitShipment = $submitShipment;
        $this->resultPageFactory = $resultPageFactory;
        $this->_shipmentFactory = $shipmentFactory;
    }

    public function execute()
    {
        $countSubmitShipment = 0;
        $shipmetHistoryIds = $this->getRequest()->getPostValue("massaction");
        foreach ($shipmetHistoryIds as $id) {
            $shipment = $this->_shipmentFactory->load($id);
            $response = $this->_submitShipment->create()->submitShipment($shipment);
            if ($response == true) {
                $countSubmitShipment++;
            }
        }
        $countNonSubmitShipment = count($shipmetHistoryIds) - $countSubmitShipment;
        if ($countSubmitShipment) {
            $string = $countSubmitShipment . ' success, ' . $countNonSubmitShipment . ' fails';
            $this->messageManager->addSuccess(__('Shipments submitted to Shiptheory : %1.', $string));
        }
        $this->_redirect('*/*/index');
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Shiptheory_Shippingx::shiptheory');
    }
}
