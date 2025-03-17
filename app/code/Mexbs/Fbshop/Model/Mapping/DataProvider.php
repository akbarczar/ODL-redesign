<?php
namespace Mexbs\Fbshop\Model\Mapping;

use Mexbs\Fbshop\Model\ResourceModel\AttributesMapping\Collection;
use Mexbs\Fbshop\Model\ResourceModel\AttributesMapping\CollectionFactory;
use Mexbs\Fbshop\Model\AttributesMapping;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * @var \Magento\SalesRule\Model\Rule\Metadata\ValueProvider
     */
    protected $metadataValueProvider;


    /**
     * Initialize dependencies.
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        /** @var AttributesMapping $mapping */
        foreach ($items as $mapping) {
            $this->loadedData[$mapping->getId()] = $mapping->getData();
        }

        return $this->loadedData;
    }
}
