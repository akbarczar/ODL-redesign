<?php

/**
 * Copyright (c) 2025 Czargroup Technologies. All rights reserved.
 *
 * @package Czargroup_ImportTracknumber
 * @author  Czargroup Technologies
 */

namespace Czargroup\ImportTracknumber\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;

/**
 * Class Orderindex
 *
 * Model class for handling order export records.
 */
class Orderindex extends AbstractModel
{
    /**
     * Orderindex constructor.
     *
     * @param Context $context Magento context.
     * @param Registry $registry Magento registry.
     * @param AbstractResource|null $resource Resource model.
     * @param AbstractDb|null $resourceCollection Collection resource.
     * @param array $data Additional data.
     */
    public function __construct(
        Context $context,
        Registry $registry,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Czargroup\ImportTracknumber\Model\ResourceModel\Orderindex::class);
    }
}
