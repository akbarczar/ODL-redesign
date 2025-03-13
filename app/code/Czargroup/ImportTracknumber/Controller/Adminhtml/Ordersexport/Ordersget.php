<?php
namespace Czargroup\ImportTracknumber\Controller\Adminhtml\Ordersexport;
use Magento\Backend\App\Action;
use Magento\Framework\File\Csv;
use Amasty\Preorder\Model\ResourceModel\OrderPreorder\CollectionFactory as PreorderCollection;
use Exception;
use Psr\Log\LoggerInterface;
use Magento\Sales\Api\Data\CreditmemoInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\CreditmemoRepositoryInterface;

class Ordersget extends \Magento\Backend\App\Action
{  
    /**
 * @var PreorderCollection
 */
private $preOrderCollection;

    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;
    /**
     * @var CreditmemoRepositoryInterface
     */
    private $creditmemoRepository;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */

    public function __construct(
    	\Magento\Backend\App\Action\Context $context,
    	\Magento\Framework\View\Result\PageFactory $resultPageFactory,
    	PreorderCollection $preOrderCollection,
		CreditmemoRepositoryInterface $creditmemoRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        LoggerInterface $logger
    ) {
    	parent::__construct($context);
    	$this->resultPageFactory = $resultPageFactory;
    	$this->preOrderCollection = $preOrderCollection;
		$this->creditmemoRepository = $creditmemoRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->logger = $logger;
    
    }
    
	/**
     * @return \Magento\Backend\Model\View\Result\Page
     */
	public function execute()
	{
		
		if ($this->getRequest()->isAjax())
		{
			$writer = new \Zend_Log_Writer_Stream(BP . '/var/log/ordersget.log');
			$logger = new \Zend_Log();
			$logger->addWriter($writer);
			$datefrom = $this->getRequest()->getParam('dateFrom');
			$logger->info("datefrom is" . $datefrom);
			$dateto = $this->getRequest()->getParam('dateTo');
			$logger->info("dateto is" . $dateto);
			$filteredBy = $this->getRequest()->getParam('filterBy');
			$logger->info("filteredBy is" . $filteredBy);
			$datepickerFrom = date('Y-m-d H:i:s',strtotime($datefrom));
			$logger->info("datepickerFrom is" . $datepickerFrom);
			$datepickerTo = date('Y-m-d',strtotime($dateto))." 23:00:00";
			$logger->info("datepickerTo is" . $datepickerTo);
			$obj = \Magento\Framework\App\ObjectManager::getInstance();
			$resource = $obj->create('\Magento\Framework\App\ResourceConnection');
			$connection = $resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
			$tblSalesOrder = $connection->getTableName('sales_order_item');
			$pa = 0;
			try
			{
				/* if($datepickerFrom == $datepickerTo){
					//$logger->info($datepickerFrom.":".$datepickerTo);
					$orders = $connection->fetchAll("SELECT * FROM $tblSalesOrder WHERE DATE(created_at) = '$datepickerFrom' AND  product_type != 'configurable' GROUP BY order_id");
					//$logger->info("after");
				}
				else{ */
					$orders = $connection->fetchAll("SELECT * FROM $tblSalesOrder WHERE created_at >= '$datepickerFrom' AND created_at <= '$datepickerTo' AND  product_type != 'configurable' GROUP BY order_id");
					//$logger->info("else");
				//}
				//$all = $datefrom."+".$dateto;
				//$logger->info(count($orders));
					if(count($orders)>0)
					{
					$mage_csv = $obj->create('\Magento\Framework\File\Csv'); //mage CSV   
					$mage_csv->setDelimiter(',');
					$mage_csv->setEnclosure('"');
					$dir = $obj->get('\Magento\Framework\App\Filesystem\DirectoryList');
					$path = $dir->getPath('var').'/custome_order_export/' ;
					//$logger->info($path);
					$name = 'orders_exported_on_'.date("Y-m-d-H-i-s").'.csv';
					//$logger->info($name);
					$file_path = $path . $name;
					//$logger->info($file_path);
					$customer_row=array(); 	$customer_row1=array(); $customer_rowN=array(); 
					//$customer_row1[] = "order_id";
					$customer_row1[] = "order_increment_id";
					$customer_row1[] = "email";
					//$customer_row1[] = "firstname";
					//$customer_row1[] = "lastname";
					$customer_row1[] = "created_at";
					$customer_row1[] = "updated_at";
					$customer_row1[] = "invoice_created_at";
					$customer_row1[] = "grand_total";
					$customer_row1[] = "total_refunded";
					$customer_row1[] = "order_status";
					$customer_row1[] = "order_state";
					$customer_row1[] = "shipping_prefix";
					$customer_row1[] = "shipping_firstname";
					$customer_row1[] = "shipping_middlename";
					$customer_row1[] = "shipping_lastname";
					$customer_row1[] = "shipping_suffix";
					$customer_row1[] = "shipping_street_full";
					$customer_row1[] = "shipping_city";
					$customer_row1[] = "shipping_region";
					$customer_row1[] = "shipping_country";
					$customer_row1[] = "shipping_postcode";
					$customer_row1[] = "shipping_telephone";
					$customer_row1[] = "shipping_company";
					$customer_row1[] = "product_sku";
					$customer_row1[] = "product_name";
					$customer_row1[] = "qty_ordered";
					$customer_row1[] = "product_type";
					$customer_row1[] = "item_shipping_status";
					$customer_row1[] = "attribute_set";
					$customer_row1[] = "order_notes";
					$customer_row1[] = "order_comments";
					$customer_row1[] = "is_preorder";
					$customer_row1[] = "returns";
					$customer_row1[] = "payment_method";
					
					$customer_row[]=$customer_row1;
					$timefc = $obj->create('\Magento\Framework\Stdlib\DateTime\TimezoneInterface');
					$prevOrderId = '';
					
					foreach($orders as $orders1)
					{

						$protype = $orders1['product_type'];
						$orderid = $orders1['order_id'];
						$order = $obj->get('Magento\Sales\Model\Order')->load($orderid);
						$orderCommentHistory = $connection->fetchAll("SELECT comment FROM sales_order_status_history WHERE parent_id=$orderid && is_custom_comment='1'");
						// $logger->info($orderid);
						// $logger->info(print_r($orderCommentHistory,true));
						$ordercomment = '';$o = 1;
						foreach($orderCommentHistory as $ordhistory){
							if($ordhistory['comment']!= ''){
								//$ordercomment = $ordercomment.$o.")".$ordhistory['comment'];
								$ordercomment = strip_tags($ordhistory['comment']);
								//$logger->info($ordercomment);
								$o++;
							}
						}
						
						$order_status = $order['status'];
						$invoice_date = '';
						$invoice_collection = '';
						$proattset_name = '';
						$_product = '';
						$product_visibility = '';
							//$logger->info(print_r($orders1,true));
						/* if ($order->hasInvoices()) {
							$invoice_collection = $order->getInvoiceCollection();
							$invoice = '';
							foreach($invoice_collection as $invoices){
								$invoice = $invoices->getData();
								$logger->info($orderid);
								$logger->info($invoice['increment_id']);
								$invoice_date = $invoice['created_at'];
							}
						} */	
						$invoices = $connection->fetchAll("SELECT * FROM sales_invoice WHERE order_id = $orderid");
						
						if(count($invoices)>0)
						{
							foreach($invoices as $invoice){
								//$logger->info($orderid);
								//$logger->info($invoice['increment_id']);
								$invoice_date1 = $timefc->formatDateTime($invoice['created_at']);
								$invoice_date = date('Y-m-d H:i:s',strtotime($invoice_date1));
							}
						}
						
						$billingaddress = $order->getBillingAddress()->getData();
						
						if(!is_null($order->getShippingAddress())){
							$shippingaddress = $order->getShippingAddress()->getData();	
						}
						else
						{
							$shippingaddress = $billingaddress;
						}											
						$created_at1 = $timefc->formatDateTime($orders1['created_at']);
						$updated_at1 = $timefc->formatDateTime($orders1['updated_at']);
						$created_at = date('Y-m-d H:i:s',strtotime($created_at1));
						$updated_at = date('Y-m-d H:i:s',strtotime($updated_at1));
						$order_number = $order->getIncrementId();
						
						$country = $obj->create('\Magento\Directory\Model\Country');
						$country1 = $country->load($shippingaddress["country_id"]);
						$region = $obj->create('\Magento\Directory\Model\Region');
						$region1 = $region->load($shippingaddress["region_id"]);

						$i = 0;
						
						//$order_items = $connection->fetchAll("SELECT * FROM sales_order_item WHERE order_id = '$orderid'");
						$order_items = $order->getAllItems();
						//$logger->info("order item ".count($order_items));
						
						// var_dump($customer_rowN);
						// die();
                        $rmastatus = '';
						
						$searchCriteria = $this->searchCriteriaBuilder
                        ->addFilter('order_id', $orderid)->create();
						$creditmemos = $this->creditmemoRepository->getList($searchCriteria);
                        $creditmemoRecords = $creditmemos->getItems();
                        $refund_return = '';
                        if(count($creditmemoRecords) > 0)
                        {
                            foreach ($creditmemoRecords as $creditmemo) {
                                $creditmemo_data = $creditmemo->getData();
                                //$logger->info($creditmemo_data['refund_reasons']);
                                $refund_return = $creditmemo_data['refund_reasons'];
                            }
                        }
						 
						$preOrderCollection = $this->preOrderCollection->create()->addFieldToFilter('order_id',$orderid);
						$is_preorder = '';
                         if($preOrderCollection){                        
                            foreach($preOrderCollection->getData() as $preOrder){
                                $is_preorder = $preOrder['is_preorder'] ? 'Yes' : 'No';
                            }                        
                        }  
                        
						
						
                        
                        $prevOrderId = $order['increment_id'];
                        foreach($order_items as $order_item)
                        {
                        	
                        	if ( $order["increment_id"] != $prevOrderId ){$pa = 0;}else{$pa++;}
							$customer_rowN["increment_id"] = $order['increment_id'];//.'_'.$pa;
							
							$customer_rowN["email"] = $billingaddress["email"];
							//$customer_rowN["firstname"] = $billingaddress["firstname"];
							//$customer_rowN["lastname"] = $billingaddress["lastname"];
							$customer_rowN["created_at"] = $created_at;
							$customer_rowN["updated_at"] = $updated_at;
							$customer_rowN["invoice_created_at"] = $invoice_date;
							$customer_rowN["grand_total"] = $order->getGrandTotal();
							$customer_rowN["total_refunded"] = $order->getTotalRefunded();
							$customer_rowN["order_status"] = $order_status;
							$customer_rowN["order_state"] = $order->getState();
							$customer_rowN["shipping_prefix"] = $shippingaddress["prefix"];
							$customer_rowN["shipping_firstname"] = strtoupper($shippingaddress["firstname"].' '.$shippingaddress["middlename"] .' '.$shippingaddress["lastname"]);
							$customer_rowN["shipping_middlename"] = '';
							$customer_rowN["shipping_lastname"] = '';
							$customer_rowN["shipping_suffix"] = $shippingaddress["suffix"];
							$customer_rowN["shipping_street_full"] = $shippingaddress["street"];
							$customer_rowN["shipping_city"] = $shippingaddress["city"];
							$customer_rowN["shipping_region"] = $region1->getName();
							$customer_rowN["shipping_country"] = $country1->getName();
							$shippingpostcode = str_replace('*','',$shippingaddress["postcode"] ?? '');
							
							$customer_rowN["shipping_postcode"] = strtoupper($shippingpostcode);
							$shippingTelephone = str_replace('o','0',$shippingaddress["telephone"] ?? '');
							$shippingTelephone = preg_replace("/[^0-9]/", "",$shippingTelephone);
							$shippingTelephone = substr($shippingTelephone, -10);
							$customer_rowN["shipping_telephone"] = $shippingTelephone;
							$customer_rowN["shipping_company"] = $shippingaddress["company"];
							
							
							
							$protype = $order_item['product_type'];
							if($protype == "simple"){
								//$logger->info("item type ".$protype);
								$sku = $order_item['sku'];
								$proidsql = $connection->fetchOne("SELECT entity_id FROM catalog_product_entity WHERE sku = '$sku'");
								if($proidsql){
									//$_product = $obj->get('Magento\Catalog\Model\Product')->load($proidsql);
									$_product = $obj->create('Magento\Catalog\Model\Product')->load($proidsql);
								//$logger->info("order pid: ".$proidsql);
								//$logger->info("order sku: ".$sku);
									$order_comment = $connection->fetchOne("SELECT value FROM catalog_product_entity_varchar WHERE entity_id = '$proidsql' AND attribute_id = '347'");
									$proattsetid = $_product->getAttributeSetId();
									$attset = $obj->get('\Magento\Eav\Api\AttributeSetRepositoryInterface');
									$attributeSetRepository = $attset->get($_product->getAttributeSetId());
									$proattset_name = $attributeSetRepository->getAttributeSetName();
									if($_product->getAttributeText('visibility')  == "Not Visible Individually"){
										$product_visibility = 'Yes';
									}else{
										$product_visibility = 'No';
									}
								}
								else{
									$proattset_name = '';
									$product_visibility = '';
								}
								
								$proname = $order_item['name'];
								$qty = $order_item['qty_ordered'];
								$customer_rowN["product_sku"] = $sku;
								$customer_rowN["product_name"] = $proname;
								$customer_rowN["qty_ordered"] = $qty;
								$customer_rowN["product_type"] = $protype;
								
								#===============================================
								$customer_rowN["item_shipping_status"] = '';
								if ($order_status == 'processing'){
									if($order_item->getStatus() == 'Mixed')
									{
										$customer_rowN["item_shipping_status"] = 'Partially Shipped';
									}
									if ($order_item->getStatus() == 'Invoiced')
									{
										$customer_rowN["item_shipping_status"] = 'Processing';//$order_item->getStatus();
									}
									if ($order_item->getStatus() == 'Shipped')
									{
										$customer_rowN["item_shipping_status"] = $order_item->getStatus();
									}
								}
								
								if ($order_status == 'complete'){
									$customer_rowN["item_shipping_status"] = 'Complete';
								}
								
								if ($order_status == 'pending'){
									$customer_rowN["item_shipping_status"] = 'Un-paid';
								}
								
								#===============================================
								
								$customer_rowN["attribute_set"] = $proattset_name;
								
								if(!empty($order_comment)) {
									//$logger->info("order note: ".$order_comment);
									$customer_rowN["order_notes"] = $order_comment;
								}
								else{
									$order_comment = '';
									$customer_rowN["order_notes"] = '';
								}

								/* if ($_product->getOrderNotes() != '' && $_product->getOrderNotes() != null){							 
								}
								
								else{ $customer_rowN["order_notes"] = ''; } */	
								$customer_rowN["order_comments"] = $ordercomment;
                                $customer_rowN["is_preorder"] = $is_preorder;
								$customer_rowN["returns"] = $refund_return;
								//$logger->info("order note: ".$order_comment);
								$payment = $order->getPayment();
								
								$customer_rowN["payment_method"] = $payment->getMethod();
								
								$customer_row[]=$customer_rowN;
								
								
								$i++;	
							}
							if ( $order["increment_id"] != $prevOrderId ){$pa++;}else{$pa=1;}
							
						}
						
						$pa = 0;
						$prevOrderId = '';
					}
					
					//$logger->info(print_r($customer_rowN,true));
					//if ($order_status = $filteredBy || $filteredBy = ''){
					$mage_csv->saveData($file_path, $customer_row);
					//}
					$this->downloadCsv($file_path);
				}
			}catch(Exception $e){
				$logger->info($e->getMessage());
			}
			//echo count($orders);
		}
	}  
	public function downloadCsv($file)
	{
		if (file_exists($file)) {
			 //set appropriate headers
			header('Content-Description: File Transfer');
			header('Content-Type: application/csv');
			header('Content-Disposition: attachment; filename='.basename($file));
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($file));
			if(ob_get_length() > 0) {
				ob_clean();
				flush();
			}
			readfile($file);
		}
	}
}