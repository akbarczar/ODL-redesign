<?php
namespace Mexbs\Fbshop\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class ScheduleStore extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('fbshop_schedule_store', 'schedule_store_id');
    }
}