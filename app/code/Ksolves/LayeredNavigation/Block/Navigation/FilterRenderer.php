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

namespace Ksolves\LayeredNavigation\Block\Navigation;

use Magento\Catalog\Model\Layer\Filter\FilterInterface;
use Magento\Catalog\Model\Layer\Filter\AbstractFilter;

/**
 * class FilterRenderer
 */
class FilterRenderer extends \Magento\LayeredNavigation\Block\Navigation\FilterRenderer
{
    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $ksPriceCurrency;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $ksStoreManager;

    /**
     * @var \Magento\Directory\Model\CurrencyFactory
     */
    protected $ksCurrencyFactory;

    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Template\Context $ksContext
     * @param \Magento\Directory\Model\Currency $ksPriceCurrency
     * @param \Magento\Directory\Model\CurrencyFactory $ksCurrencyFactory
     * @param \Magento\Store\Model\StoreManagerInterface $ksStoreManager
     * @param array $ksData
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $ksContext,
        \Magento\Framework\Pricing\PriceCurrencyInterface $ksPriceCurrency,
        \Magento\Directory\Model\CurrencyFactory $ksCurrencyFactory,
        \Magento\Store\Model\StoreManagerInterface $ksStoreManager,
        array $ksData = []
    ) {
        $this->ksPriceCurrency = $ksPriceCurrency;
        $this->ksStoreManager = $ksStoreManager;
        $this->ksCurrencyFactory = $ksCurrencyFactory;
        parent::__construct($ksContext, $ksData);
    }
    /**
     * @param FilterInterface $filter
     * @return string
     */
    public function render(FilterInterface $ksFilter)
    {
        $this->assign('filterItems', $ksFilter->getItems());
        $this->assign('filter', $ksFilter);
        $ksHtml = $this->_toHtml();
        $this->assign('filterItems', []);
        return $ksHtml;
    }

    /**
     *  Get minimum and maximum price range
     * @param AbstractFilter $filter
     * @return string
     */
    public function getCategoryProductsPriceRange(AbstractFilter $ksFilter)
    {
        $ksMinPrice = 0;
        $ksMaxPrice = 100000000;
        if ($ksFilter->getLayer()->getProductCollection()->getSize()>0) {
            $max = array_column($ksFilter->getLayer()->getProductCollection()->getData(), 'max_price');
            if($max){
                $ksMaxPrice = max($max);
            }

            $min = array_column($ksFilter->getLayer()->getProductCollection()->getData(), 'min_price');

            if($min){
                $ksMinPrice= min(array_column($ksFilter->getLayer()->getProductCollection()->getData(), 'min_price'));
            }
        }

        $ksPriceRange['min'] = floor($ksMinPrice);
        $ksPriceRange['max'] = ceil($ksMaxPrice);

        return $ksPriceRange;
    }


    /**
     * Get url
     * @param  $filter
     * @return string
     */
    public function getProductsFilterUrl($filter)
    {
        $ksquery = ['price'=> ''];
        return $this->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true, '_query' => $ksquery]);
    }

    /**
     * Get current currency symbol
     * @return string
     */
    public function getKsConvertAndFormat($ksPrice)
    {
        return $this->ksPriceCurrency->convertAndFormat($ksPrice);
    }

    /**
     * Get current currency symbol
     * @return string
     */
    public function getKsCurrencySymbol()
    {
        return $this->ksPriceCurrency->getCurrencySymbol();
    }

    /**
     * Get current currency symbol
     * @return string
     */
    public function getKsCurrencyRate()
    {
        $ksCurrencyCodeTo = $this->ksStoreManager->getStore()->getCurrentCurrency()->getCode();
        $ksCurrencyCodeFrom = $this->ksStoreManager->getStore()->getBaseCurrency()->getCode();

        $ksRate = $this->ksCurrencyFactory->create()->load($ksCurrencyCodeFrom)->getAnyRate($ksCurrencyCodeTo);
        return $ksRate;
    }

    /**
     * Get total products
     * @param AbstractFilter $ksFilter
     * @return int
     */
    public function getTotalProducts(AbstractFilter $ksFilter)
    {
        return $ksFilter->getLayer()->getProductCollection()->getSize();
    }
}
