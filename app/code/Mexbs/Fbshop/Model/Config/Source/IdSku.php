<?php
namespace Mexbs\Fbshop\Model\Config\Source;

class IdSku implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'id', 'label' => __('ID')],
            ['value' => 'sku', 'label' => __('SKU')]
        ];
    }
}