<?php
namespace Mexbs\Fbshop\Model\Config\Source;

class HourADay implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        $optionArray = [];
        for($hour = 0; $hour < 24; $hour++){
            $optionArray[] = [
                'value' => $hour, 'label' => $hour
            ];
        }
        return $optionArray;
    }
}
