<?php
namespace Mexbs\Fbshop\Model\Config\Source;

class FeedFileLocation implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'static', 'label' => __('pub/static directory')],
            ['value' => 'root', 'label' => __('Root directory')]
        ];
    }
}