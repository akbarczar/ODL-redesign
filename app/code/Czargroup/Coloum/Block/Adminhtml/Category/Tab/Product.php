<?php
namespace Czargroup\Coloum\Block\Adminhtml\Category\Tab;

class Product extends \Magento\Catalog\Block\Adminhtml\Category\Tab\Product
{
    /**
     * Set collection object
     *
     * @param \Magento\Framework\Data\Collection $collection
     * @return void
     */
    public function setCollection($collection)
    {
        $collection->addAttributeToSelect('warehouse_instock');
        parent::setCollection($collection);
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();
        $this->addColumnAfter('warehouse_instock', array(
            'header' => __('Warehouse Instock'),
            'index' => 'warehouse_instock',
        ), 'sku');

        $this->sortColumnsByOrder();
        return $this;
    }
}