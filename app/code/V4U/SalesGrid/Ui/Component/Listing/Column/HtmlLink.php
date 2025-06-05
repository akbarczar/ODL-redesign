<?php

namespace V4U\SalesGrid\Ui\Component\Listing\Column;


use Magento\Framework\Escaper;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\UrlInterface;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Sales\Api\OrderRepositoryInterface; 

class HtmlLink extends Column {
	
    protected $escaper;
    protected $collectionFactory;
    protected $orderRepository;
	
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        Escaper $escaper,
		CollectionFactory $collectionFactory,
		OrderRepositoryInterface $orderRepository,
        array $components = [],
        array $data = []
    ) {
        $this->escaper = $escaper;
		$this->collectionFactory = $collectionFactory;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource){
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
			
            foreach ($dataSource['data']['items'] as & $item) {
				$value = $item['custom_field'];
				if ($value) {
                    $item[$fieldName] = $value;
                } else {
                    $item[$fieldName] = '';
                }
            } 
        }
        return $dataSource;
    }

}