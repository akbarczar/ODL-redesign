<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ExtendedOrdersGrid\Plugin;

use Magento\Sales\Model\ResourceModel\Order\Grid\Collection as OrderGridCollection;

/**
 * Class AddDataToOrdersGrid
 */
class AddDataToOrdersGrid
{
    private $logger;

    public function __construct(
        \Psr\Log\LoggerInterface $customLogger,
        array $data = []
    ) {
        $this->logger = $customLogger;
    }

    public function afterGetReport($subject, $collection, $requestName)
    {
        if ($requestName !== 'sales_order_grid_data_source') {
            return $collection;
        }

        if ($collection->getMainTable() === $collection->getResource()->getTable('sales_order_grid')) {
            try {
                $orderAddressTableName           = $collection->getResource()->getTable('sales_order_address');
                $directoryCountryRegionTableName = $collection->getResource()->getTable('directory_country_region');

                $collection->getSelect()->joinLeft(
                    ['soat' => $orderAddressTableName],
                    'soat.parent_id = main_table.entity_id AND soat.address_type = \'shipping\'',
                    ['telephone']
                );

                $collection->getSelect()->joinLeft(
                    ['dcrt' => $directoryCountryRegionTableName],
                    'soat.region_id = dcrt.region_id',
                    ['code']
                );

                $this->addProductSkuColumn($collection);
                $this->addRefundReasonColumn($collection);
                $this->addOrderedQtyColumn($collection);
            } catch (\Zend_Db_Select_Exception $selectException) {
                $this->logger->log(100, $selectException);
            }
        }

        return $collection;
    }

    private function addProductSkuColumn(OrderGridCollection $collection): OrderGridCollection
    {
        $orderItemsTableName = $collection->getResource()->getTable('sales_order_item');

        $itemsTableSelectGrouped = $collection->getConnection()->select();

        $itemsTableSelectGrouped->from(
            $orderItemsTableName,
            [
                'name'     => new \Zend_Db_Expr('GROUP_CONCAT(DISTINCT name SEPARATOR \',\')'),
                'sku'      => new \Zend_Db_Expr('GROUP_CONCAT(DISTINCT sku SEPARATOR \',\')'),
                'order_id' => 'order_id'
            ]
        );

        $itemsTableSelectGrouped->group('order_id');

        $collection->getSelect()
                   ->joinLeft(
                       ['soi' => $itemsTableSelectGrouped],
                       'soi.order_id = main_table.entity_id',
                       ['name', 'sku']
                   );

        return $collection;
    }

    private function addRefundReasonColumn(OrderGridCollection $collection): OrderGridCollection
    {
        $orderItemsTableName = $collection->getResource()->getTable('sales_creditmemo');

        $credetmemoTableSelectGrouped = $collection->getConnection()->select();

        $credetmemoTableSelectGrouped->from(
            $orderItemsTableName,
            [
                'refund_reasons' => new \Zend_Db_Expr('GROUP_CONCAT(DISTINCT refund_reasons SEPARATOR \',\')'),
                'order_id'       => 'order_id'
            ]
        );

        $credetmemoTableSelectGrouped->group('order_id');

        $collection->getSelect()
                   ->joinLeft(
                       ['cmt' => $credetmemoTableSelectGrouped],
                       'cmt.order_id = main_table.entity_id',
                       ['refund_reasons']
                   );

        return $collection;
    }

    private function addOrderedQtyColumn(OrderGridCollection $collection): OrderGridCollection
    {
        $orderItemsTableName = $collection->getResource()->getTable('sales_order_item');

        $itemsTableSelectGrouped = $collection->getConnection()->select();

        $itemsTableSelectGrouped->from(
            $orderItemsTableName,
            [
                'ordered_qty' => new \Zend_Db_Expr('SUM(qty_ordered)'),
                'order_id'    => 'order_id'
            ]
        );

        $itemsTableSelectGrouped->group('order_id');

        $collection->getSelect()
                   ->joinLeft(
                       ['soi_qty' => $itemsTableSelectGrouped],
                       'soi_qty.order_id = main_table.entity_id',
                       ['ordered_qty']
                   );

        return $collection;
    }
}

