<?php
namespace Mexbs\Fbshop\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class AttributesMapping extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('fbshop_attributes_mapping', 'mapping_id');
    }
}