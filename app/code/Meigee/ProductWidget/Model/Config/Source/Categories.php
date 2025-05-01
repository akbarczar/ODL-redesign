<?php
namespace Meigee\ProductWidget\Model\Config\Source;

use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollection;
 
class Categories implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    private $categoryCollection;

    /**
     * Categories constructor.
     * @param CategoryCollection $categoryCollection
     */
    public function __construct(CategoryCollection $categoryCollection) {$this->categoryCollection = $categoryCollection;}

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
	public function toOptionArray()
    {
        $collection = $this->categoryCollection->create();
        $collection->addAttributeToSelect('name');
        $options = [];
        foreach ($collection as $category) {
            $options[] = ['label' => $category->getName(), 'value' => $category->getId()];
        }
        return $options;
    }
}
