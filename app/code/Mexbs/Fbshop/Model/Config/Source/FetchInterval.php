<?php
namespace Mexbs\Fbshop\Model\Config\Source;

class FetchInterval implements \Magento\Framework\Option\ArrayInterface
{
    const DAILY = 'DAILY';
    const HOURLY = 'HOURLY';

    public function toOptionArray()
    {
        return [
            ['value' => self::DAILY, 'label' => __('Daily')],
            ['value' => self::HOURLY, 'label' => __('Hourly')]
        ];
    }
}
