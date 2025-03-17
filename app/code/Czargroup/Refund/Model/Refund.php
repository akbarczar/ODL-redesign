<?php

namespace Czargroup\Refund\Model;

use Magento\Framework\Model\AbstractModel;
use Czargroup\Refund\Model\ResourceModel\Refund as ResourceModel;

class Refund extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }
}