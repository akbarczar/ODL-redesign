<?php
namespace Mexbs\Fbshop\Model;

use Magento\Framework\Model\AbstractModel;

class AttributesMapping extends AbstractModel
{
    protected function _construct()
    {
        $this->_init('Mexbs\Fbshop\Model\ResourceModel\AttributesMapping');
    }
}