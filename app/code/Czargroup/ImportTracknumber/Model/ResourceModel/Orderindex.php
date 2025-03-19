<?php

/**
 * Copyright (c) 2025 Czargroup Technologies. All rights reserved.
 *
 * @package Czargroup_ImportTracknumber
 * @author  Czargroup Technologies
 */

namespace Czargroup\ImportTracknumber\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

/**
 * Class Orderindex
 *
 * Resource model for the Orderindex entity.
 */
class Orderindex extends AbstractDb
{
    /**
     * Orderindex constructor.
     *
     * @param Context $context Database context.
     * @param string|null $resourcePrefix Optional resource prefix.
     */
    public function __construct(
        Context $context,
        $resourcePrefix = null
    ) {
        parent::__construct($context, $resourcePrefix);
    }

    /**
     * Initialize database table and primary key.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('czargroup_exported_files', 'id');
    }
}
