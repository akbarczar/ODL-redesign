<?php

namespace Czargroup\SimpleQtyObserver\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable as ConfigurableType;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Psr\Log\LoggerInterface;

class StockItemSaveAfterObserver implements ObserverInterface
{
    protected $configurableType;
    protected $productRepository;
    protected $stockRegistry;
    protected $logger;
	 /**
     * @var Magento\CatalogInventory\Api\StockStateInterface 
     */
    protected $_stockStateInterface;

    public function __construct(
        ConfigurableType $configurableType,
        ProductRepositoryInterface $productRepository,
        StockRegistryInterface $stockRegistry,
        LoggerInterface $logger,
		 \Magento\CatalogInventory\Api\StockStateInterface $stockStateInterface
    ) {
        $this->configurableType = $configurableType;
        $this->productRepository = $productRepository;
        $this->stockRegistry = $stockRegistry;
        $this->logger = $logger;
		$this->_stockStateInterface = $stockStateInterface;
    }

   public function execute(Observer $observer)
{
    // Log writer for debugging
    $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/config.log');
    $logger = new \Zend_Log();
    $logger->addWriter($writer);

    $logger->info('Observer triggered.');

    // Get the bunch of products being imported
    $bunch = $observer->getBunch();

    $allParentIds = [];
    $processedConfigurableProducts = []; // Track processed configurable products

    foreach ($bunch as $productData) {
        try {
            // Load the product by SKU
            $product = $this->productRepository->get($productData['sku']);

            // Check if the product is a simple product
            if ($product->getTypeId() === 'simple') {
                //$updatedSimpleProducts[] = $productData['sku'];
				$parentIds = $this->configurableType->getParentIdsByChild($product->getId());

				if (!empty($parentIds)) {
					foreach ($parentIds as $parentId) {
						$allParentIds[] = $parentId;
						/* if (in_array($parentId, $processedConfigurableProducts)) {
							//$logger->info('Skipping already processed Configurable Product ID: ' . $parentId);
							continue;
						} */
						
						
					}
					//$logger->info('saved product' . $parentId);
				}
            }
        } catch (\Exception $e) {
            $logger->err('Error loading product for SKU: ' . $productData['sku'] . ' - ' . $e->getMessage());
        }
    }

	//$logger->info(print_r($allParentIds, true));
    if (empty($allParentIds)) {
        $logger->info('No simple products found in this batch.');
        return;
    }
	$allParentIds = array_values(array_unique($allParentIds));
	$logger->info('final parentIds');
	$logger->info(print_r($allParentIds, true));
foreach ($allParentIds as $parentId) {
    $logger->info('Processing product: ' . $parentId);
    try {
        // Load ObjectManager
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        
        // Load repositories and stock management classes
        $productRepository = $objectManager->get(\Magento\Catalog\Api\ProductRepositoryInterface::class);
        $stockRegistry = $objectManager->get(\Magento\CatalogInventory\Api\StockRegistryInterface::class);
        $stockIndexerProcessor = $objectManager->get(\Magento\CatalogInventory\Model\Indexer\Stock\Processor::class);

        // Load configurable product
        $configProduct = $productRepository->getById($parentId);

        // Load stock item
        $stockItem = $stockRegistry->getStockItem($configProduct->getId());

        // Set product out of stock temporarily
        $stockItem->setIsInStock(false);
        $stockRegistry->updateStockItemBySku($configProduct->getSku(), $stockItem);
        $logger->info('Set product OOS: ' . $parentId);

        // Set product back to in stock
        $stockItem->setIsInStock(true);
        $stockRegistry->updateStockItemBySku($configProduct->getSku(), $stockItem);
        $logger->info('Set product IS: ' . $parentId);

        // Reindex stock status
        $stockIndexerProcessor->reindexRow($configProduct->getId());

        $logger->info('Stock reindexed for: ' . $parentId);
    } catch (\Exception $e) {
        $logger->err('Error processing product ' . $parentId . ': ' . $e->getMessage());
    }
}


    //$logger->info('Updated Simple Products: ' . implode(', ', $allParentIds));
//     foreach ($allParentIds as $parentId) {
// 		$logger->info('Product save -- ' .$parentId);
//         try {
//             $configProduct = $this->productRepository->getById($parentId);
//             //$configurableProduct = $this->productRepository->getById($parentId);
// 			$stockItem = $this->stockRegistry->getStockItem($configProduct->getId());
// 			$stockItem->setData('is_in_stock',0);
// 			$stockItem->setData('manage_stock',1);
// 			$stockItem->save(); 
// 			$logger->info('Product saved with OOS id ' .$parentId);
// 			$stockItem->setData('is_in_stock',1);
// 			$stockItem->setData('manage_stock',1);
// 			$stockItem->save(); 
// 			$logger->info('Product saved with IS id ' .$parentId);
//         } catch (\Exception $e) {
//             $logger->err('Error processing SKU ' . $parentId . ': ' . $e->getMessage());
//         }
//     }
}


    /**
     * Check stock status of a configurable product based on child products.
     *
     * @param \Magento\Catalog\Model\Product $configurableProduct
     * @return bool
     */
    private function checkConfigurableStock($configurableProduct)
    {
        $childProducts = $this->configurableType->getChildrenIds($configurableProduct->getId());
        foreach ($childProducts as $childIds) {
            foreach ($childIds as $childId) {
                $stockItem = $this->stockRegistry->getStockItem($childId);
                if ($stockItem->getQty() > 0 && $stockItem->getIsInStock()) {
                    return true; // At least one child product is in stock
                }
            }
        }
        return false; // All child products are out of stock
    }
}
