<?php
namespace Mexbs\Fbshop\Model\Mapping\Source;

use Magento\Framework\Data\OptionSourceInterface;

class ProductCustomOptionTitle implements OptionSourceInterface{
    protected $options;
    protected $customOptionsCollectionFactory;

    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\Option\CollectionFactory $customOptionsCollectionFactory
    ) {
        $this->customOptionsCollectionFactory = $customOptionsCollectionFactory;
    }


    public function toOptionArray()
    {
        if ($this->options !== null) {
            return $this->options;
        }

        $options = [];

        /**
         * @var \Magento\Catalog\Model\ResourceModel\Product\Option\Collection $customOptionsCollection
         */
        $customOptionsCollection = $this->customOptionsCollectionFactory->create();
        $customOptionsCollection->addTitleToResult(\Magento\Store\Model\Store::DEFAULT_STORE_ID);
        $customOptionsCollection->getSelect()->where(
          sprintf("type IN (%s)", $customOptionsCollection->getConnection()->quote([
              \Magento\Catalog\Model\Product\Option::OPTION_TYPE_DROP_DOWN,
              \Magento\Catalog\Model\Product\Option::OPTION_TYPE_RADIO
          ]))
        )->group('title');


        foreach ($customOptionsCollection as $customOption) {
            /**
             * @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute
             */
            $options[] = [
                'label' => $customOption->getTitle(),
                'value' => $customOption->getTitle(),
            ];
        }
        $this->options = $options;

        return $this->options;
    }
}