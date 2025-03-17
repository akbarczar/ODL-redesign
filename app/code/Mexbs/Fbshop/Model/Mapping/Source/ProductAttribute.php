<?php
namespace Mexbs\Fbshop\Model\Mapping\Source;

use Magento\Framework\Data\OptionSourceInterface;

class ProductAttribute implements OptionSourceInterface{
    protected $options;
    protected $attributesCollection;

    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attributeCollectionFactory
    ) {
        $this->attributesCollection = $attributeCollectionFactory->create();
    }


    public function toOptionArray()
    {
        if ($this->options !== null) {
            return $this->options;
        }

        $options = [];
        foreach ($this->attributesCollection as $attribute) {
            /**
             * @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute
             */
            $options[] = [
                'label' => $attribute->getAttributeCode(),
                'value' => $attribute->getAttributeCode(),
            ];
        }
        $this->options = $options;

        return $this->options;
    }
}