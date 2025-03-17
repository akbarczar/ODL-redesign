<?php
namespace Mexbs\Fbshop\Model\ResourceModel\AttributesMapping;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Mexbs\Fbshop\Model\AttributesMapping', 'Mexbs\Fbshop\Model\ResourceModel\AttributesMapping');
    }
}