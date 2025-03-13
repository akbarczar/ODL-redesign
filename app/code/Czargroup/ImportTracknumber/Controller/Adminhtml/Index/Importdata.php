<?php
/**
 * Copyright (c) 2025 Czargroup Technologies. All rights reserved.
 *
 * @package Czargroup_ImportTracknumber
 * @author Czargroup Technologies
 */
namespace Czargroup\ImportTracknumber\Controller\Adminhtml\Index;
use Magento\Backend\App\Action;
use Magento\Framework\File\Csv;

class Importdata extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * @var \Magento\Framework\File\Csv
     */
    protected $_fileCsv;

     /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Magento\Sales\Model\Order\Shipment\TrackFactory
     */
    protected $_shipmentTrackFactory;

    /**
     * @var \Magento\Sales\Model\Order\ShipmentFactory
     */
    protected $_shipmentFactory;

    /**
     * @var \Magento\Framework\DB\TransactionFactory
     */
    protected $_transactionFactory;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $_orderRepository;

    /**
     * @var \Magento\Sales\Model\Order\Email\Sender\ShipmentSender
     */
    protected $_shipmentSender;

    /**
     * @var \Magento\Shipping\Model\ShipmentNotifier
     */
    protected $_shipmentNotifier;

    /**
     * Importdata constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Framework\File\Csv $fileCsv
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Sales\Model\Order\Shipment\TrackFactory $shipmentTrackFactory
     * @param \Magento\Sales\Model\Order\ShipmentFactory $shipmentFactory
     * @param \Magento\Framework\DB\TransactionFactory $transactionFactory
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Sales\Model\Order\Email\Sender\ShipmentSender $shipmentSender
     * @param \Magento\Shipping\Model\ShipmentNotifier $shipmentNotifier
     */
    public function __construct(
    	\Magento\Backend\App\Action\Context $context, 
    	\Magento\Framework\View\Result\PageFactory $resultPageFactory, 
    	\Magento\Framework\App\ResourceConnection $resource, 
    	\Magento\Framework\File\Csv $fileCsv, 
    	\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory, 
    	\Magento\Sales\Model\Order\Shipment\TrackFactory $shipmentTrackFactory, 
    	\Magento\Sales\Model\Order\ShipmentFactory $shipmentFactory, 
    	\Magento\Framework\DB\TransactionFactory $transactionFactory, 
    	\Magento\Sales\Api\OrderRepositoryInterface $orderRepository, 
    	\Magento\Sales\Model\Order\Email\Sender\ShipmentSender $shipmentSender, 
    	\Magento\Shipping\Model\ShipmentNotifier $shipmentNotifier
    ){
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->resource = $resource;
        $this->_fileCsv = $fileCsv;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_shipmentTrackFactory = $shipmentTrackFactory;
        $this->_shipmentFactory = $shipmentFactory;
        $this->_transactionFactory = $transactionFactory;
        $this->_orderRepository = $orderRepository;
        $this->_shipmentSender = $shipmentSender;
        $this->_shipmentNotifier = $shipmentNotifier;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        if ($this->getRequest()
            ->isAjax())
        {
            $connection = $this
                ->resource
                ->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
            $tblSalesOrder = $connection->getTableName('sales_order_item');
            $import_message = '';
            try
            {
                $csvfile = $this->getRequest()
                    ->getParam('filepath');
                if (file_exists($csvfile))
                {
                    $data = $this
                        ->_fileCsv
                        ->getData($csvfile);
                    $import_message = $this->importdata($data);
                }
                $resultJson = $this
                    ->resultJsonFactory
                    ->create();
                return $resultJson->setData(['importmsg' => $import_message]);
            }
            catch(\Exception $e)
            {
                throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()));
            }
        }
    }

    /**
     * Imports data from the CSV file
     *
     * @param array $data
     * @return string
     */
    protected function importdata($data)
    {
        $connection = $this
            ->resource
            ->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        $tblSalesOrder = $connection->getTableName('sales_order');
        $shipment_exist = 'Shipment already exists for order number(s) ';
        $shipment_success = 'Data imported successfully for ';
        $cancel_message = 'Can not create shipment for order number(s) ';
        $existflag = 0;
        $successflag = 0;
        $success_num = 0;
        $cancelflag = 0;
        for ($i = 1;$i < count($data);$i++)
        {
            $incr_id = $data[$i][0];
            $orderid = $connection->fetchOne("SELECT entity_id FROM $tblSalesOrder WHERE increment_id = $incr_id");
            //$orderid = $orderid[0];
            $title = $data[$i][1];
            $tracknum = $data[$i][2];
            $ship_status_temp = $this->createShipment($orderid, $title, $tracknum);
            if ($ship_status_temp === true)
            {
                $successflag = 1;
                $success_num++;
            }
            elseif ($ship_status_temp == "Canceled")
            {
                $cancelflag = 1;
                $cancel_message .= $incr_id . " ";
            }
            elseif (!$ship_status_temp)
            {
                $existflag = 1;
                $shipment_exist .= $incr_id . " ";
            }
        }
        $import_msgs = '';
        if ($existflag == 1 && $successflag == 1 && $cancelflag == 1)
        {
            $import_msgs = $shipment_success . $success_num . " records..!" . "</br>" . $shipment_exist . "</br>" . $cancel_message;
        }
        elseif ($existflag == 1 && $successflag == 1)
        {
            $import_msgs = $shipment_success . $success_num . " records..!" . "</br>" . $shipment_exist;
        }
        elseif ($existflag == 1 && $cancelflag == 1)
        {
            $import_msgs = $shipment_exist . "</br>" . $cancel_message;
        }
        elseif ($successflag == 1 && $cancelflag == 1)
        {
            $import_msgs = $shipment_success . $success_num . " records..!" . "</br>" . $cancel_message;
        }
        elseif ($existflag == 1)
        {
            $import_msgs = $shipment_exist;
        }
        elseif ($cancelflag == 1)
        {
            $import_msgs = $cancel_message;
        }
        else
        {
            $import_msgs = $shipment_success . $success_num . " records..!";
        }
        return $import_msgs;
    }

    /**
     * Creates a shipment for a given order and tracking number
     *
     * @param int $orderId
     * @param string $title
     * @param string $trackingNumber
     * @return bool|string
     */
    protected function createShipment($orderId, $title, $trackingNumber)
    {
        try
        {
            $order = $this
                ->_orderRepository
                ->get($orderId);
            $shipment_exist = '';
            if ($order)
            {
                if ($order->hasShipments())
                {
                    return false;
                }
                else
                {
                    $data = [['carrier_code' => 'custom', 'title' => $title, 'number' => $trackingNumber, ], ];
                    if ($order->canShip())
                    {
                        $shipment = $this->prepareShipment($order, $data);
                        if ($shipment)
                        {
                            $order->setIsInProcess(true);
                            $order->addStatusHistoryComment('Shipment created by Import', false);
                            $transactionSave = $this
                                ->_transactionFactory
                                ->create()
                                ->addObject($shipment)->addObject($shipment->getOrder());
                            $transactionSave->save();
                            $this
                                ->_shipmentSender
                                ->send($shipment);
                            $shipment_success = true;
                        }
                        return $shipment_success;
                    }
                    else
                    {
                        return "Canceled";
                    }
                }
            }
        }
        catch(\Exception $e)
        {
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()));
        }
    }

    /**
     * Prepares the shipment object
     *
     * @param \Magento\Sales\Model\Order $order
     * @param array $track
     * @return \Magento\Sales\Model\Order\Shipment|false
     */
    protected function prepareShipment($order, $track)
    {
        $shipment = $this
            ->_shipmentFactory
            ->create($order, $this->prepareShipmentItems($order) , $track);
        return $shipment->getTotalQty() ? $shipment->register() : false;
    }

    /**
     * Prepares shipment items
     *
     * @param \Magento\Sales\Model\Order $order
     * @return array
     */
    protected function prepareShipmentItems($order)
    {
        $items = [];
        foreach ($order->getAllItems() as $item)
        {
            $items[$item->getItemId() ] = $item->getQtyOrdered();
        }
        return $items;
    }
}

