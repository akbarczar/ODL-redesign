<?php

/**
 * Copyright (c) 2025 Czargroup Technologies. All rights reserved.
 *
 * @package Czargroup_ImportTracknumber
 * @author Czargroup Technologies
 */

namespace Czargroup\ImportTracknumber\Controller\Adminhtml\Orderindex;

use Magento\Backend\App\Action;
use Magento\Framework\File\Csv;
use Amasty\Preorder\Model\ResourceModel\OrderPreorder\CollectionFactory as PreorderCollection;
use Exception;
use Magento\Sales\Api\Data\CreditmemoInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\CreditmemoRepositoryInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class Ordersget
 *
 * Handles exporting of orders.
 */
class Ordersget extends \Magento\Backend\App\Action
{
	/**
	 * @var preorderCollection
	 */
	protected $preOrderCollection;
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
	 * Constructor for the Ordersget controller.
	 *
	 * @param \Magento\Backend\App\Action\Context $context Context object for backend actions.
	 * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory Factory for creating result pages.
	 * @param preorderCollection $preOrderCollection Collection of pre-orders.
	 * @param CreditmemoRepositoryInterface $creditmemoRepository Repository interface for managing credit memos.
	 * @param SearchCriteriaBuilder $searchCriteriaBuilder Builder for creating search criteria.
	 */
	public function __construct(
		\Magento\Backend\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory,
		preorderCollection $preOrderCollection,
		CreditmemoRepositoryInterface $creditmemoRepository,
		SearchCriteriaBuilder $searchCriteriaBuilder
	) {
		parent::__construct($context);
		$this->resultPageFactory = $resultPageFactory;
		$this->preOrderCollection = $preOrderCollection;
		$this->creditmemoRepository = $creditmemoRepository;
		$this->searchCriteriaBuilder = $searchCriteriaBuilder;
	}

	/**
	 * Export orders to a CSV file.
	 * 
	 * @return \Magento\Backend\Model\View\Result\Page
	 */
	public function execute()
	{

		if ($this->getRequest()->isAjax()) {
			$datefrom = $this->getRequest()->getParam('dateFrom');
			$dateto = $this->getRequest()->getParam('dateTo');
			$filteredBy = $this->getRequest()->getParam('filterBy');
			$datepickerFrom = date('Y-m-d H:i:s', strtotime($datefrom));
			$datepickerTo = date('Y-m-d', strtotime($dateto)) . " 23:00:00";
			$obj = \Magento\Framework\App\ObjectManager::getInstance();
			$resource = $obj->create('\Magento\Framework\App\ResourceConnection');
			$connection = $resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
			$tblSalesOrder = $connection->getTableName('sales_order_item');
			$pa = 0;
			try {
				if (!empty($filteredBy) && strtolower($filteredBy) != 'all') {
					if (strtolower($filteredBy) == 'cancelled') {
						$mappedStatus = 'canceled';
					} elseif (strtolower($filteredBy) == 'partial refund') {
						$mappedStatus = 'closed';
					} else {
						$mappedStatus = $filteredBy;
					}
				}

				$tblSalesOrderItem = $connection->getTableName('sales_order_item');
				$tblSalesOrder = $connection->getTableName('sales_order');

				$query = "SELECT soi.* FROM $tblSalesOrderItem AS soi
                    INNER JOIN $tblSalesOrder AS so ON soi.order_id = so.entity_id
                    WHERE soi.created_at >= '$datepickerFrom'
                    AND soi.created_at <= '$datepickerTo'
                    AND soi.product_type != 'configurable'";

				if (!empty($filteredBy) && strtolower($filteredBy) != 'all') {
					$query .= " AND so.status = '$mappedStatus'";
				}

				$query .= " GROUP BY soi.order_id";

				$orders = $connection->fetchAll($query);

				if (count($orders) > 0) {
					$mage_csv = $obj->create('\Magento\Framework\File\Csv');
					$mage_csv->setDelimiter(',');
					$mage_csv->setEnclosure('"');
					$dir = $obj->get('\Magento\Framework\App\Filesystem\DirectoryList');
					$path = $dir->getPath('var') . '/custome_order_export/';
					$name = 'orders_exported_on_' . date("Y-m-d-H-i-s") . '.csv';
					$filePath = "custome_order_export/" . $name;

					$exportedFile = $this->_objectManager->create('Czargroup\ImportTracknumber\Model\Orderindex');
					$exportedFile->setFileName($name);
					$exportedFile->setFilePath($filePath);
					$exportedFile->save();
					$file_path = $path . $name;
					$customer_row = array();
					$customer_row1 = array();
					$customer_rowN = array();
					$customer_row1[] = "order_increment_id";
					$customer_row1[] = "email";
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

					$customer_row[] = $customer_row1;
					$timefc = $obj->create('\Magento\Framework\Stdlib\DateTime\TimezoneInterface');
					$prevOrderId = '';

					foreach ($orders as $orders1) {

						$protype = $orders1['product_type'];
						$orderid = $orders1['order_id'];
						$order = $obj->get('Magento\Sales\Model\Order')->load($orderid);
						$orderCommentHistory = $connection->fetchAll("SELECT comment FROM sales_order_status_history WHERE parent_id=$orderid && is_custom_comment='1'");
						$ordercomment = '';
						$o = 1;
						foreach ($orderCommentHistory as $ordhistory) {
							if ($ordhistory['comment'] != '') {
								$ordercomment = strip_tags($ordhistory['comment']);
								$o++;
							}
						}

						$order_status = $order['status'];
						$invoice_date = '';
						$invoice_collection = '';
						$proattset_name = '';
						$_product = '';
						$product_visibility = '';
						$invoices = $connection->fetchAll("SELECT * FROM sales_invoice WHERE order_id = $orderid");

						if (count($invoices) > 0) {
							foreach ($invoices as $invoice) {
								$invoice_date1 = $timefc->formatDateTime($invoice['created_at']);
								$invoice_date = date('Y-m-d H:i:s', strtotime($invoice_date1));
							}
						}

						$billingaddress = $order->getBillingAddress()->getData();

						if (!is_null($order->getShippingAddress())) {
							$shippingaddress = $order->getShippingAddress()->getData();
						} else {
							$shippingaddress = $billingaddress;
						}
						$created_at1 = $timefc->formatDateTime($orders1['created_at']);
						$updated_at1 = $timefc->formatDateTime($orders1['updated_at']);
						$created_at = date('Y-m-d H:i:s', strtotime($created_at1));
						$updated_at = date('Y-m-d H:i:s', strtotime($updated_at1));
						$order_number = $order->getIncrementId();

						$country = $obj->create('\Magento\Directory\Model\Country');
						$country1 = $country->load($shippingaddress["country_id"]);
						$region = $obj->create('\Magento\Directory\Model\Region');
						$region1 = $region->load($shippingaddress["region_id"]);

						$i = 0;

						$order_items = $order->getAllItems();
						$rmastatus = '';

						$searchCriteria = $this->searchCriteriaBuilder
							->addFilter('order_id', $orderid)->create();
						$creditmemos = $this->creditmemoRepository->getList($searchCriteria);
						$creditmemoRecords = $creditmemos->getItems();
						$refund_return = '';
						if (count($creditmemoRecords) > 0) {
							foreach ($creditmemoRecords as $creditmemo) {
								$creditmemo_data = $creditmemo->getData();
								$refund_return = $creditmemo_data['refund_reasons'];
							}
						}

						$preOrderCollection = $this->preOrderCollection->create()->addFieldToFilter('order_id', $orderid);
						$is_preorder = '';
						if ($preOrderCollection) {
							foreach ($preOrderCollection->getData() as $preOrder) {
								$is_preorder = $preOrder['is_preorder'] ? 'Yes' : 'No';
							}
						}


						$prevOrderId = $order['increment_id'];
						foreach ($order_items as $order_item) {

							if ($order["increment_id"] != $prevOrderId) {
								$pa = 0;
							} else {
								$pa++;
							}
							$customer_rowN["increment_id"] = $order['increment_id'];

							$customer_rowN["email"] = $billingaddress["email"];
							$customer_rowN["created_at"] = $created_at;
							$customer_rowN["updated_at"] = $updated_at;
							$customer_rowN["invoice_created_at"] = $invoice_date;
							$customer_rowN["grand_total"] = $order->getGrandTotal();
							$customer_rowN["total_refunded"] = $order->getTotalRefunded();
							$customer_rowN["order_status"] = $order_status;
							$customer_rowN["order_state"] = $order->getState();
							$customer_rowN["shipping_prefix"] = $shippingaddress["prefix"];
							$customer_rowN["shipping_firstname"] = strtoupper($shippingaddress["firstname"] . ' ' . $shippingaddress["middlename"] . ' ' . $shippingaddress["lastname"]);
							$customer_rowN["shipping_middlename"] = '';
							$customer_rowN["shipping_lastname"] = '';
							$customer_rowN["shipping_suffix"] = $shippingaddress["suffix"];
							$customer_rowN["shipping_street_full"] = $shippingaddress["street"];
							$customer_rowN["shipping_city"] = $shippingaddress["city"];
							$customer_rowN["shipping_region"] = $region1->getName();
							$customer_rowN["shipping_country"] = $country1->getName();
							$shippingpostcode = str_replace('*', '', $shippingaddress["postcode"] ?? '');

							$customer_rowN["shipping_postcode"] = strtoupper($shippingpostcode);
							$shippingTelephone = str_replace('o', '0', $shippingaddress["telephone"] ?? '');
							$shippingTelephone = preg_replace("/[^0-9]/", "", $shippingTelephone);
							$shippingTelephone = substr($shippingTelephone, -10);
							$customer_rowN["shipping_telephone"] = $shippingTelephone;
							$customer_rowN["shipping_company"] = $shippingaddress["company"];


							$protype = $order_item['product_type'];
							if ($protype == "simple") {
								$sku = $order_item['sku'];
								$proidsql = $connection->fetchOne("SELECT entity_id FROM catalog_product_entity WHERE sku = '$sku'");
								if ($proidsql) {
									$_product = $obj->create('Magento\Catalog\Model\Product')->load($proidsql);
									$order_comment = $connection->fetchOne("SELECT value FROM catalog_product_entity_varchar WHERE entity_id = '$proidsql' AND attribute_id = '347'");
									$proattsetid = $_product->getAttributeSetId();
									$attset = $obj->get('\Magento\Eav\Api\AttributeSetRepositoryInterface');
									$attributeSetRepository = $attset->get($_product->getAttributeSetId());
									$proattset_name = $attributeSetRepository->getAttributeSetName();
									if ($_product->getAttributeText('visibility')  == "Not Visible Individually") {
										$product_visibility = 'Yes';
									} else {
										$product_visibility = 'No';
									}
								} else {
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
								if ($order_status == 'processing') {
									if ($order_item->getStatus() == 'Mixed') {
										$customer_rowN["item_shipping_status"] = 'Partially Shipped';
									}
									if ($order_item->getStatus() == 'Invoiced') {
										$customer_rowN["item_shipping_status"] = 'Processing';
									}
									if ($order_item->getStatus() == 'Shipped') {
										$customer_rowN["item_shipping_status"] = $order_item->getStatus();
									}
								}

								if ($order_status == 'complete') {
									$customer_rowN["item_shipping_status"] = 'Complete';
								}

								if ($order_status == 'pending') {
									$customer_rowN["item_shipping_status"] = 'Un-paid';
								}

								#===============================================

								$customer_rowN["attribute_set"] = $proattset_name;

								if (!empty($order_comment)) {
									$customer_rowN["order_notes"] = $order_comment;
								} else {
									$order_comment = '';
									$customer_rowN["order_notes"] = '';
								}
								$customer_rowN["order_comments"] = $ordercomment;
								$customer_rowN["is_preorder"] = $is_preorder;
								$customer_rowN["returns"] = $refund_return;
								$payment = $order->getPayment();

								$customer_rowN["payment_method"] = $payment->getMethod();

								$customer_row[] = $customer_rowN;


								$i++;
							}
							if ($order["increment_id"] != $prevOrderId) {
								$pa++;
							} else {
								$pa = 1;
							}
						}

						$pa = 0;
						$prevOrderId = '';
					}
					$mage_csv->saveData($file_path, $customer_row);
					$this->messageManager->addSuccessMessage(__('Orders exported successfully'));
					return $this->_redirect('*/*/');
				}
			} catch (Exception $e) {
				$this->messageManager->addErrorMessage(__('An error occurred while exporting orders.'));
				return $this->_redirect('*/*/');
			}
		}
	}
}
