<?php

namespace Czargroup\Refund\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Refund extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('my_cars', 'car_id');
    }
}