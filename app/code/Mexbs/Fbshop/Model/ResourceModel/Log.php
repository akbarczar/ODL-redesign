<?php
namespace Mexbs\Fbshop\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Log extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('fbshop_log', 'log_id');
    }
}