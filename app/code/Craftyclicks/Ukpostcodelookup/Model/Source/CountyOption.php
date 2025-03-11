<?php

namespace Craftyclicks\Ukpostcodelookup\Model\Source;

class CountyOption implements \Magento\Framework\Data\OptionSourceInterface
{
	/**
	* @return array
	*/
	public function toOptionArray()
	{
		return [
			['value' => 'none', 'label' => __('Empty Field')],
			['value' => 'former_postal', 'label' => __('Former Postal Counties')],
			['value' => 'traditional', 'label' => __('Traditional Counties')]
		];
	}
}
