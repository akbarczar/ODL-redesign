<?php
namespace Mexbs\Fbshop\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class CustomOptionsMapping extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('fbshop_custom_options_mapping', 'mapping_id');
    }
}