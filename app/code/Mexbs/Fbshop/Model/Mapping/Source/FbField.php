<?php
namespace Mexbs\Fbshop\Model\Mapping\Source;

use Magento\Framework\Data\OptionSourceInterface;
use \FacebookAds\Object\ProductItem;

class FbField implements OptionSourceInterface{
    protected $options;
    public function toOptionArray()
    {
        if ($this->options !== null) {
            return $this->options;
        }

        $options = [];
        foreach (\Mexbs\Fbshop\Helper\Data::$fields as $value) {
            if(!in_array($value, \Mexbs\Fbshop\Helper\Data::$defaultCompletedBySystemFields)){
                $options[] = [
                    'label' => $value,
                    'value' => $value,
                ];
            }
        }
        $this->options = $options;

        return $this->options;
    }
}