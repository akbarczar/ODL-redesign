<?php
/**
 * Ksolves
 *
 * @category  Ksolves
 * @package   Ksolves_LayeredNavigation
 * @author    Ksolves Team
 * @copyright Copyright (c) Ksolves India Limited (https://www.ksolves.com/)
 * @license   https://store.ksolves.com/magento-license
 */

namespace Ksolves\LayeredNavigation\Model\Layer\Filter;

use Magento\CatalogSearch\Model\Layer\Filter\Price as AbstractFilter;

/**
 * Layer price filter
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Price extends AbstractFilter
{
    /**
     * @var \Magento\Catalog\Model\Layer\Filter\DataProvider\Price
     */
    private $dataProvider;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var \Ksolves\LayeredNavigation\Helper\Data
     */
    protected $ksHelperData;

    /**
     * Price constructor.
     * @param \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Layer $layer
     * @param \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder
     * @param \Magento\Catalog\Model\ResourceModel\Layer\Filter\Price $resource
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Search\Dynamic\Algorithm $priceAlgorithm
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Catalog\Model\Layer\Filter\Dynamic\AlgorithmFactory $algorithmFactory
     * @param \Magento\Catalog\Model\Layer\Filter\DataProvider\PriceFactory $dataProviderFactory
     * @param \Ksolves\LayeredNavigation\Helper\Data $helperData
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Layer $layer,
        \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder,
        \Magento\Catalog\Model\ResourceModel\Layer\Filter\Price $resource,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Search\Dynamic\Algorithm $priceAlgorithm,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Catalog\Model\Layer\Filter\Dynamic\AlgorithmFactory $algorithmFactory,
        \Magento\Catalog\Model\Layer\Filter\DataProvider\PriceFactory $dataProviderFactory,
        \Ksolves\LayeredNavigation\Helper\Data $ksHelperData,
        array $data = []
    ) {
        parent::__construct(
            $filterItemFactory,
            $storeManager,
            $layer,
            $itemDataBuilder,
            $resource,
            $customerSession,
            $priceAlgorithm,
            $priceCurrency,
            $algorithmFactory,
            $dataProviderFactory,
            $data
        );

        $this->priceCurrency = $priceCurrency;
        $this->dataProvider  = $dataProviderFactory->create(['layer' => $this->getLayer()]);
        $this->ksHelperData = $ksHelperData;
    }

    /**
     * Apply price range filter
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return $this
     */
    public function apply(\Magento\Framework\App\RequestInterface $ksRequest)
    {
        if (!$this->ksHelperData->getPriceSlider()) {
            return parent::apply($ksRequest);
        }
        /**
         * Filter must be string: $fromPrice-$toPrice
         */
        $filter = $ksRequest->getParam($this->getRequestVar());
        if (!$filter || is_array($filter)) {
            $this->filterValue = false;
            return $this;
        }

        //validate filter
        $filterParams = explode(',', $filter);
        $filter       = $this->dataProvider->validateFilter($filterParams[0]);
        if (!$filter) {
            $this->filterValue = false;
            return $this;
        }

        $this->dataProvider->setInterval($filter);
        $priorFilters = $this->dataProvider->getPriorFilters($filterParams);
        if ($priorFilters) {
            $this->dataProvider->setPriorIntervals($priorFilters);
        }

        list($ksFrom, $ksTo) = $filter;
        $this->getLayer()->getProductCollection()->addFieldToFilter(
            'price',
            ['from' => $ksFrom, 'to' => $ksTo]
        );

        $this->getLayer()->getState()->addFilter(
            $this->_createItem($this->_renderRangeLabel(empty($ksFrom) ? 0 : $ksFrom, $ksTo), $filter)
        );

        return $this;
    }

    /**
     * Prepare text of range label
     *
     * @param float|string $fromPrice
     * @param float|string $toPrice
     * @return float|\Magento\Framework\Phrase
     */
    protected function _renderRangeLabel($ksFromPrice, $ksToPrice, $isLast = false)
    {
        if (!$this->ksHelperData->getPriceSlider()) {
            return parent::_renderRangeLabel($ksFromPrice, $ksToPrice);
        }
        $ksFormattedFromPrice = $this->priceCurrency->convertAndFormat($ksFromPrice);
        if ($ksToPrice === '') {
            return __('%1 and above', $ksFormattedFromPrice);
        } elseif ($ksFromPrice == $ksToPrice && $this->dataProvider->getOnePriceIntervalValue()) {
            return $ksFormattedFromPrice;
        } else {
            return __('%1 - %2', $ksFormattedFromPrice, $this->priceCurrency->convertAndFormat($ksToPrice));
        }
    }

    /**
     * Get data array for building attribute filter items
     * @return array
     */
   protected function _getItemsData()
    {
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$appState = $objectManager->get('\Magento\Framework\View\DesignInterface');
		 if($appState->getDesignTheme()->getCode()=='Plumrocket/amp_one') {
			if ($this->ksHelperData->getPriceSlider()) {
				return parent::_getItemsData();
			}
		}
        if (!$this->ksHelperData->getPriceSlider()) {
            return parent::_getItemsData();
        }

         $ksData = [[
            'label' => 'Ksolves Price Slider test',
            'value' => '0-100',
            'count' => 1,
            'from'  => '0',
            'to'    => '100',
        ]];
        return $ksData; 
    }
}
