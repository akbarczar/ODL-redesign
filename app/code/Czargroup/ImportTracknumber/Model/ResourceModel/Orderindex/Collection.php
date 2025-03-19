<?php

/**
 * Copyright (c) 2025 Czargroup Technologies. All rights reserved.
 *
 * @package Czargroup_ImportTracknumber
 * @author  Czargroup Technologies
 */

namespace Czargroup\ImportTracknumber\Model\ResourceModel\Orderindex;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Czargroup\ImportTracknumber\Model\Orderindex as Model;
use Czargroup\ImportTracknumber\Model\ResourceModel\Orderindex as ResourceModel;

/**
 * Class Collection
 *
 * Collection class for Orderindex model.
 */
class Collection extends AbstractCollection
{
    /**
     * Initialize collection model and resource model.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
        $this->_idFieldName = 'id';
    }
}
