<?php

namespace Shiptheory\Shippingx\Block\Adminhtml\History;

use Shiptheory\Shippingx\Block\Adminhtml\History\Renderer\ShipmentLink;
use Shiptheory\Shippingx\Block\Adminhtml\History\Renderer\User;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $_shipmentHistoryFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Shiptheory\Shippingx\Model\ShipmentHistory $shipmentHistoryFactory,
        array $data = []
    ) {
        $this->_shipmentHistoryFactory = $shipmentHistoryFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('postGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(false);
        $this->setVarNameFilter('post_filter');
    }

    protected function _prepareCollection()
    {
        $collection = $this->_shipmentHistoryFactory->getCollection();
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'id',
            [
                'header' => __('#'),
                'type' => 'number',
                'index' => 'id',
                'width' => '50px',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            'created_at',
            [
                'header' => __('Date'),
                'index' => 'created_at',
                'type' => 'datetime',
            ]
        );
        $this->addColumn(
            'shipment_id',
            [
                'header' => __('Shipment'),
                'index' => 'shipment_id',
                'renderer' => ShipmentLink::class,
            ]
        );
        $this->addColumn(
            'user_id',
            [
                'header' => __('User'),
                'index' => 'user_id',
                'renderer' => User::class,
            ]
        );
        $this->addColumn(
            'success',
            [
                'header' => __('Success'),
                'index' => 'success',
                'type' => 'options',
                'options' => ["1" => "Yes", "0" => "no"]
            ]
        );
        $this->addColumn(
            'message',
            [
                'header' => __('Message'),
                'index' => 'message',
            ]
        );
        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('shiptheory/*/index', ['_current' => true]);
    }

    public function getRowUrl($row)
    {
        return '#' . $row->getId();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('shipment_id');
        $this->getMassactionBlock()->addItem(
            'massSubmitShiptheory',
            [
                'label' => __('Resend to Shiptheory'),
                'url' => $this->getUrl('*/*/MassSubmitShipTheory')
            ]
        );
    }

    protected function _prepareMassactionColumn()
    {
        $columnId = 'massaction';
        $massactionColumn = $this->getLayout()
            ->createBlock(\Magento\Backend\Block\Widget\Grid\Column::class)
            ->setData(
                [
                    'index' => $this->getMassactionIdField(),
                    'filter_index' => $this->getMassactionIdFilter(),
                    'type' => 'massaction',
                    'name' => $this->getMassactionBlock()->getFormFieldName(),
                    'is_system' => true,
                    'header_css_class' => 'col-select',
                    'column_css_class' => 'col-select',
                    'use_index' => 1
                ]
            );
        if ($this->getNoFilterMassactionColumn()) {
            $massactionColumn->setData('filter', false);
        }
        $massactionColumn->setSelected($this->getMassactionBlock()->getSelected())->setGrid($this)->setId($columnId);
        $this->getColumnSet()->insert(
            $massactionColumn,
            count($this->getColumnSet()->getColumns()) + 1,
            false,
            $columnId
        );
        return $this;
    }
}
