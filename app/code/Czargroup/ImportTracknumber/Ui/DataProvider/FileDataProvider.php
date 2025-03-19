<?php

/**
 * Copyright (c) 2025 Czargroup Technologies. All rights reserved.
 *
 * @package Czargroup_ImportTracknumber
 * @author  Czargroup
 */

namespace Czargroup\ImportTracknumber\Ui\DataProvider;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Czargroup\ImportTracknumber\Model\ResourceModel\Orderindex\CollectionFactory;

/**
 * Class FileDataProvider
 *
 * Provides data for the file listing grid.
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
     * @param string $name Data provider name.
     * @param string $primaryFieldName Primary field name.
     * @param string $requestFieldName Request field name.
     * @param CollectionFactory $collectionFactory Collection factory instance.
     * @param array $meta Meta information.
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
     * Retrieves data for the UI grid.
     *
     * @return array The data array with total records and items.
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
