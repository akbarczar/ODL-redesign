<?php

/**
 * Copyright (c) 2025 Czargroup Technologies. All rights reserved.
 *
 * @package Czargroup_ImportTracknumber
 * @author  Czargroup Technologies
 */

namespace Czargroup\ImportTracknumber\Model\DataProvider;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Czargroup\ImportTracknumber\Model\ResourceModel\Orderindex\CollectionFactory;

/**
 * Class FileDataProvider
 *
 * Provides data for the UI component grid.
 */
class FileDataProvider extends AbstractDataProvider
{
    /**
     * @var \Czargroup\ImportTracknumber\Model\ResourceModel\Orderindex\Collection
     */
    protected $collection;

    /**
     * FileDataProvider constructor.
     *
     * @param string $name The name of the data provider.
     * @param string $primaryFieldName The primary field name.
     * @param string $requestFieldName The request field name.
     * @param CollectionFactory $collectionFactory The collection factory.
     * @param array $meta Additional metadata.
     * @param array $data Additional data.
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
    }

    /**
     * Retrieve data for UI component.
     *
     * @return array The data array containing total records and items.
     */
    public function getData()
    {
        if (!$this->getCollection()->isLoaded()) {
            $this->getCollection()->load();
        }

        $items = $this->getCollection()->toArray();

        return [
            'totalRecords' => $this->getCollection()->getSize(),
            'items' => array_values($items['items']),
        ];
    }
}
