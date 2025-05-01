<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Meigee\ProductWidget\Block\Product;

use Magento\Customer\Model\Context as CustomerContext;
use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Widget\Block\BlockInterface;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\CatalogWidget\Model\Rule;
use Magento\Widget\Helper\Conditions;
use Magento\Rule\Model\Condition\Sql\Builder;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Magento\Framework\App\ResourceConnection;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Block\Product\Widget\Html\Pager;
use Magento\Catalog\Model\Product;
use Magento\Framework\Pricing\Render;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\Framework\View\Element\AbstractBlock;

/**
 * Products widget
 */
class ProductsList extends AbstractProduct implements BlockInterface, IdentityInterface
{
    /**
     * Default value for products count that will be shown
     */
    const DEFAULT_PRODUCTS_COUNT = 10;

    /**
     * Default value for number products per row on desktop
     */
    const DEFAULT_PRODUCTS_PER_ROW = 4;

    /**
     * Default value for number products per row on mac
     */
    const DEFAULT_PRODUCTS_PER_ROW_MAC = 4;

    /**
     * Default value for number products per row on vertical tablet
     */
    const DEFAULT_PRODUCTS_PER_ROW_TABLET = 3;

    /**
     * Default value for number products per row on mobile
     */
    const DEFAULT_PRODUCTS_PER_ROW_MOBILE = 1;

    /**
     * Default value for number visible products on desktop
     */
    const DEFAULT_VISIBLE_PRODUCTS = 4;

    /**
     * Default value for number visible products on vertical tablet
     */
    const DEFAULT_VISIBLE_PRODUCTS_VERTICAL_TABLET = 3;

    /**
     * Default value for number visible products on mobile
     */
    const DEFAULT_VISIBLE_PRODUCTS_MOBILE = 1;

    /**
     * Default value for navigation enable
     */
    const DEFAULT_NAVIGATION_ENABLE = 1;

    /**
     * Default value for navigation position
     */
    const DEFAULT_NAVIGATION_POSITION = 'inside_container';

    /**
     * Navigation position top right
     */
    const NAVIGATION_POSITION_TOP_RIGHT = 'top_right';

    /**
     * Navigation position inside slider
     */
    const NAVIGATION_POSITION_INSIDE_CONTAINER = 'inside_container';

    /**
     * Default value for pagination enable
     */
    const DEFAULT_PAGINATION_ENABLE = 0;

    /**
     * Default value for autoplay enable
     */
    const DEFAULT_AUTOPLAY_ENABLE = 0;

    /**
     * Default value for autoplay delay
     */
    const DEFAULT_AUTOPLAY_DELAY = 5000;

    /**na
     * Default value for slider rtl layout
     */
    const DEFAULT_SLIDER_RTL_LAYOUT_ENABLE = 0;

    /**
     * Display products type - all products
     */
    const DISPLAY_TYPE_ALL_PRODUCTS = 'all_products';

    /**
     * Media to generate css class on desktop
     */
    const MEDIA_CSS_LG = 'lg';

    /**
     * Media to generate css class on mac
     */
    const MEDIA_CSS_MD = 'md';

    /**
     * Media to generate css class on vertical tablet
     */
    const MEDIA_CSS_SM = 'sm';

    /**
     * Media to generate css class on mobile
     */
    const MEDIA_CSS_XS = 'xs';

    /**
     * Display products type - new products
     */
    const DISPLAY_TYPE_NEW_PRODUCTS = 'newproducts';

    /**
     * Display products type - sale products
     */
    const DISPLAY_TYPE_SALE_PRODUCTS = 'saleproducts';

    /**
     * Display products type - best sellers products
     */
    const DISPLAY_TYPE_BEST_SELLERS_PRODUCTS = 'bestsellers';

    /**
     * Display products type - featured category products
     */
    const DISPLAY_TYPE_FEATURED_CATEGORY_PRODUCTS = 'featuredcategory';

    /**
     * Default value whether show pager or not
     */
    const DEFAULT_SHOW_PAGER = false;

    /**
     * Default value for products per page
     */
    const DEFAULT_PRODUCTS_PER_PAGE = 5;

    /**
     * Default value for collection sort by key
     */
    const DEFAULT_COLLECTION_SORT_BY = 'name';

    /**
     * Default value for collection sort order
     */
    const DEFAULT_COLLECTION_SORT_ORDER = 'asc';

    /**
     * Default value for products count that will be shown
     */
    const FEATURED_CATEGORY_ID = 1;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    private $httpContext;

    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * Product collection factory
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var \Magento\Widget\Helper\Conditions
     */
    private $conditionsHelper;

    /**
     * @var \Magento\CatalogWidget\Model\Rule
     */
    private $rule;

    /**
     * @var \Magento\Rule\Model\Condition\Sql\Builder
     */
    private $sqlBuilder;

    /**
     * The json serializer.
     *
     * @var JsonSerializer
     */
    private $jsonSerializer;

    /**
     * Instance of pager block
     *
     * @var \Magento\Catalog\Block\Product\Widget\Html\Pager
     */
    private $pager;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resource;

    /**
     * Catalog product visibility
     *
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    private $catalogProductVisibility;

    /**
     * ProductsList constructor.
     * @param Context $context
     * @param CollectionFactory $productCollectionFactory
     * @param HttpContext $httpContext
     * @param CategoryRepositoryInterface $categoryRepository
     * @param Rule $rule
     * @param Conditions $conditionsHelper
     * @param Builder $sqlBuilder
     * @param JsonSerializer $jsonSerializer
     * @param ResourceConnection $resource
     * @param Visibility $catalogProductVisibility
     * @param array $data
     */
    public function __construct(
        Context $context,
        CollectionFactory $productCollectionFactory,
        HttpContext $httpContext,
        CategoryRepositoryInterface $categoryRepository,
        Rule $rule,
        Conditions $conditionsHelper,
        Builder $sqlBuilder,
        JsonSerializer $jsonSerializer,
        ResourceConnection $resource,
        Visibility $catalogProductVisibility,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $data
        );
        $this->productCollectionFactory = $productCollectionFactory;
        $this->httpContext = $httpContext;
        $this->categoryRepository = $categoryRepository;
        $this->rule = $rule;
        $this->conditionsHelper = $conditionsHelper;
        $this->sqlBuilder = $sqlBuilder;
        $this->jsonSerializer = $jsonSerializer;
        $this->resource = $resource;
        $this->catalogProductVisibility = $catalogProductVisibility;
    }

    protected function _construct()
    {
        parent::_construct();
        $this->addColumnCountLayoutDepend('empty', 6)
            ->addColumnCountLayoutDepend('1column', 5)
            ->addColumnCountLayoutDepend('2columns-left', 4)
            ->addColumnCountLayoutDepend('2columns-right', 4)
            ->addColumnCountLayoutDepend('3columns', 3);

        $this->addData([
            'cache_lifetime' => 86400,
            'cache_tags' => [Product::CACHE_TAG,
            ], ]);
    }

    /**
     * Get Key pieces for caching block content
     *
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCacheKeyInfo()
    {
        $conditions = $this->getData('conditions')
            ? $this->getData('conditions')
            : $this->getData('conditions_encoded');
        $products = $this->getProductCollection()->getItems() && $this->getProductCollection()->getSize() ?
            $this->getProductCollection()->getItems() :
            null;
        
        if(!is_null($products)){
           $products = array_values($products);  // ignore array keys
           $products = json_encode($products);
           $products = strval($products);
           $products = sha1($products); // use hashing to hide potentially private data
        }
        
        return [
            'MEIGEE_PRODUCT',
            $products,
            $this->_storeManager->getStore()->getId(),
            $this->_design->getDesignTheme()->getId(),
            $this->getWidgetType(),
            $this->getWidgetId(),
            $this->httpContext->getValue(CustomerContext::CONTEXT_GROUP),
            $this->getProductsCount(),
            $this->getProductsPerPage(),
            $this->getProductItemMediaCss(),
            $this->getSliderRtlLayoutEnable(),
            $this->getJsonConfig(),
            intval($this->getRequest()->getParam($this->getData('page_var_name'), 1)),
            $conditions,
            $this->jsonSerializer->serialize($this->getRequest()->getParams()),
            'template' => $this->getTemplate(),
            $this->getTitle()
        ];
    }

    /**
     * Prepare collection with products
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _beforeToHtml()
    {
        $this->setProductCollection($this->getProductCollection());
        return parent::_beforeToHtml();
    }

    /**
     * Product collection initialize process
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection|Object|\Magento\Framework\Data\Collection
     */
    protected function getProductCollection()
    {
        switch ($this->getWidgetType()) {
            case self::DISPLAY_TYPE_NEW_PRODUCTS:
                $collection = $this->getNewProductsCollection();
                break;
            case self::DISPLAY_TYPE_SALE_PRODUCTS:
                $collection = $this->getSaleProductsCollection();
                break;
            case self::DISPLAY_TYPE_BEST_SELLERS_PRODUCTS:
                $collection = $this->getBestSellersProductsCollection();
                break;
            case self::DISPLAY_TYPE_FEATURED_CATEGORY_PRODUCTS:
                $collection = $this->getFeaturedCategoryProductsCollection();
                break;
            default:
                $collection = $this->getRecentlyAddedProductsCollection();
                break;
        }

        $collection = $collection->setPageSize($this->getPageSize())
            ->setCurPage($this->getCurrentPage())
            ->setOrder($this->getSortBy(), $this->getSortOrder());

        $conditions = $this->getConditions();
        $conditions->collectValidatedAttributes($collection);
        $this->sqlBuilder->attachConditionToCollection($collection, $conditions);

        /**
         * Prevent retrieval of duplicate records. This may occur when multiselect product attribute matches
         * several allowed values from condition simultaneously
         */
        $collection->distinct(true);
        return $collection;
    }

    /**
     * Prepare collection for recent product list
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection|Object|\Magento\Framework\Data\Collection
     */
    protected function getRecentlyAddedProductsCollection()
    {
        /** @var $collection \Magento\Catalog\Model\ResourceModel\Product\Collection */
        $collection = $this->productCollectionFactory->create();
        $collection->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds());

        $collection = $this->_addProductAttributesAndPrices($collection)
            ->addStoreFilter()
            ->addAttributeToSort('created_at', 'desc')
            ->setPageSize($this->getPageSize())
            ->setCurPage($this->getCurrentPage());
        return $collection;
    }

    /**
     * Prepare and return product collection
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection|Object|\Magento\Framework\Data\Collection
     */
    protected function getNewProductsCollection()
    {
        $todayStartOfDayDate = $this->_localeDate->date()->setTime(0, 0, 0)->format('Y-m-d H:i:s');
        $todayEndOfDayDate = $this->_localeDate->date()->setTime(23, 59, 59)->format('Y-m-d H:i:s');

        /** @var $collection \Magento\Catalog\Model\ResourceModel\Product\Collection */
        $collection = $this->productCollectionFactory->create();
        $collection->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds());

        $collection = $this->_addProductAttributesAndPrices($collection) //create function
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('image')
            ->addAttributeToSelect('small_image')
            ->addAttributeToSelect('thumbnail')
            ->addStoreFilter()
            ->addAttributeToFilter(
                'news_from_date',
                [
                    'or' => [
                        0 => ['date' => true, 'to' => $todayEndOfDayDate],
                        1 => ['is' => new \Zend_Db_Expr('null')],
                    ]
                ],
                'left'
            )->addAttributeToFilter(
                'news_to_date',
                [
                    'or' => [
                        0 => ['date' => true, 'from' => $todayStartOfDayDate],
                        1 => ['is' => new \Zend_Db_Expr('null')],
                    ]
                ],
                'left'
            )->addAttributeToFilter(
                [
                    ['attribute' => 'news_from_date', 'is' => new \Zend_Db_Expr('not null')],
                    ['attribute' => 'news_to_date', 'is' => new \Zend_Db_Expr('not null')],
                ]
            );
        return $collection;
    }

    /**
     * Prepare and return product collection
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection|Object|\Magento\Framework\Data\Collection
     */
    protected function getSaleProductsCollection()
    {
        /** @var $collection \Magento\Catalog\Model\ResourceModel\Product\Collection */
        $collection = $this->productCollectionFactory->create();
        $collection->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds());
        $todayDate = date('Y-m-d');

        $collection = $this->_addProductAttributesAndPrices($collection) //create function
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('image')
            ->addAttributeToSelect('small_image')
            ->addAttributeToSelect('thumbnail')
            ->addStoreFilter()
            ->addAttributeToFilter('special_price', ['neq' => ''])
            ->addAttributeToFilter('special_from_date', ['lteq' => date('Y-m-d G:i:s', strtotime($todayDate))])
            ->addAttributeToFilter(array(
                array(
                    'attribute' => 'special_to_date',
                    'null' => true),
                array(
                    'attribute' => 'special_to_date',
                    'eq' => ''),
                array(
                    'attribute' => 'special_to_date',
                    'gteq' => date('Y-m-d G:i:s', strtotime($todayDate)))
                
            ))
            ->addAttributeToFilter('is_saleable', [1], 'left');
        return $collection;
    }

    /**
     * Prepare and return bestsellers product collection
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection|Object|\Magento\Framework\Data\Collection
     */
    protected function getBestSellersProductsCollection()
    {
        /** @var $collection \Magento\Catalog\Model\ResourceModel\Product\Collection */
        $collection = $this->productCollectionFactory->create();
        $connection  = $this->resource->getConnection();

        $collection = $this->_addProductAttributesAndPrices($collection) //create function
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('image')
            ->addAttributeToSelect('small_image')
            ->addAttributeToSelect('thumbnail')
            ->addStoreFilter();
            //->addAttributeToFilter('is_saleable', ['eq' => 1], 'left');

        //TODO BestSellers collection
        $collection->getSelect()
            ->joinLeft(['soi' => $connection->getTableName('sales_order_item')], 'soi.product_id = e.entity_id', ['SUM(soi.qty_ordered) AS ordered_qty'])
            ->join(['order' => $connection->getTableName('sales_order')], "order.entity_id = soi.order_id",['order.state'])
            ->where("order.state <> 'canceled' and soi.parent_item_id IS NULL AND soi.product_id IS NOT NULL")
            ->group('soi.product_id');

        return $collection;
    }

    /**
     * Prepare and return product collection
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection|Object|\Magento\Framework\Data\Collection
     */
    protected function getFeaturedCategoryProductsCollection()
    {
        // $categoryId = $this->getFeaturedCategoryId();
        // $category = $this->categoryRepository->get($categoryId);

        // /** @var $collection \Magento\Catalog\Model\ResourceModel\Product\Collection */
        // $collection = $this->productCollectionFactory->create();

        // $collection = $this->_addProductAttributesAndPrices($collection) //create function
        //     ->addAttributeToSelect('name')
        //     ->addAttributeToSelect('image')
        //     ->addAttributeToSelect('small_image')
        //     ->addAttributeToSelect('thumbnail')
        //     ->addStoreFilter()
        //     ->addCategoryFilter($category);
        // return $collection;

        /** @var $collection \Magento\Catalog\Model\ResourceModel\Product\Collection */
        $collection = $this->productCollectionFactory->create();
        $connection  = $this->resource->getConnection();
        $collection->addAttributeToSelect('*');
        $collection->addCategoriesFilter(['in' => '465']);
        $collection->setPageSize(6); // fetching only 6 products
        return $collection;


    }

    /**
     * Generate css hidden class
     *
     * @param $isGenerateClass
     * @param $media
     * @return bool|string
     */
    protected function getHiddenClass($isGenerateClass, $media)
    {
        if (!$isGenerateClass) return false;
        $mediaClass = ' hidden-' . $media;
        return $mediaClass;
    }

    /**
     * Generate css media class
     *
     * @param $cols
     * @param $media
     * @return string
     */
    protected function getMediaClass($cols, $media)
    {
        switch ($cols) :
            case 5:
                $col = '1_5';
                break;
            case 7:
                $col = '1_7';
                break;
            case 8:
                $col = '1_8';
                break;
            default:
                $col = 12/$cols;
        endswitch;

        $mediaClass = ' col-' . $media . '-' . $col;
        return $mediaClass;
    }

    /**
     *  Get value of grid widget number products per row on desktop parameter
     *
     * @return string
     */
    public function getProductItemCssLg()
    {
        if (!$this->hasData('products_per_row')) {
            $this->setData('products_per_row', self::DEFAULT_PRODUCTS_PER_ROW);
        }
        return $this->getData('products_per_row');
    }

    /**
     *  Get value of grid widget number products per row on desktop parameter
     *
     * @return string
     */
    public function getProductItemCssMd()
    {
        if (!$this->hasData('products_per_row')) {
            $this->setData('products_per_row', self::DEFAULT_PRODUCTS_PER_ROW);
        }
        return $this->getData('products_per_row');
    }

    /**
     *  Get value of grid widget number products per row on vertical tablet parameter
     *
     * @return string
     */
    public function getProductItemCssSm()
    {
        if (!$this->hasData('products_per_row_tablet')) {
            $this->setData('products_per_row_tablet', self::DEFAULT_PRODUCTS_PER_ROW_TABLET);
        }
        return $this->getData('products_per_row_tablet');
    }

    /**
     *  Get value of grid widget number products per row on mobile parameter
     *
     * @return string
     */
    public function getProductItemCssXs()
    {
        if (!$this->hasData('products_per_row_mobile')) {
            $this->setData('products_per_row_mobile', self::DEFAULT_PRODUCTS_PER_ROW_MOBILE);
        }
        return $this->getData('products_per_row_mobile');
    }

    /**
     * Generate media css classes of grid widge on all devices
     *
     * @return string
     */
   public function getProductItemMediaCss()
    {
        $productItemMediaCss = $this->getMediaClass($this->getProductItemCssLg(), self::MEDIA_CSS_LG);
        $productItemMediaCss .= $this->getMediaClass($this->getProductItemCssMd(), self::MEDIA_CSS_MD);
        $productItemMediaCss .= $this->getMediaClass($this->getProductItemCssSm(), self::MEDIA_CSS_SM);
        $productItemMediaCss .= $this->getMediaClass($this->getProductItemCssXs(), self::MEDIA_CSS_XS);
        return trim($productItemMediaCss);
    }

    /**
    *  Get value of slider widget visible products on desktop parameter
    *
    * @return string
    */
    public function getVisibleProducts()
    {
        if (!$this->hasData('visible_products')) {
            $this->setData('visible_products', self::DEFAULT_VISIBLE_PRODUCTS);
        }
        return $this->getData('visible_products');
    }

    /**
     *  Get value of slider widget visible products on vertical tablet parameter
     *
     * @return string
     */
    public function getVisibleProductsTablet()
    {
        if (!$this->hasData('visible_products_tablet')) {
            $this->setData('visible_products_tablet', self::DEFAULT_VISIBLE_PRODUCTS_VERTICAL_TABLET);
        }
        return $this->getData('visible_products_tablet');
    }

    /**
     *  Get value of slider widget visible products on mobile parameter
     *
     * @return string
     */
    public function getVisibleProductsMobile()
    {
        if (!$this->hasData('visible_products_mobile')) {
            $this->setData('visible_products_mobile', self::DEFAULT_VISIBLE_PRODUCTS_MOBILE);
        }
        return $this->getData('visible_products_mobile');
    }

    /**
     *  Get value of slider widget navigation enable parameter
     *
     * @return string
     */
    public function getNavigationEnable()
    {
        if (!$this->hasData('navigation')) {
            $this->setData('navigation', self::DEFAULT_NAVIGATION_ENABLE);
        }
        return $this->stringToBoolean($this->getData('navigation'));
    }

    /**
     * Generate navigation js option option for slider widget
     *
     * @param bool $isShow
     * @return array|bool
     */
    public function getNavigationJsOption($isShow = false)
    {
        if($this->getNavigationEnable() || $isShow){
            return [
                'nextEl' => '#swiper-navigation-' . $this->getWidgetId() . ' .swiper-button-next',
                'prevEl' => '#swiper-navigation-' . $this->getWidgetId() . ' .swiper-button-prev'
            ];
        }
        return false;
    }

    /**
     * Generate breakpoints for slider widget navigation
     *
     * @return string
     */
    public function getSliderNavigationBreakpoints()
    {
        $sliderNavigationBreakpoints = $this->getHiddenClass(!$this->getNavigationEnable(), self::MEDIA_CSS_LG);
        $sliderNavigationBreakpoints .= $this->getHiddenClass(!$this->getNavigationEnable(), self::MEDIA_CSS_MD);
        $sliderNavigationBreakpoints .= $this->getHiddenClass(($this->getNavigationPosition() !== self::NAVIGATION_POSITION_TOP_RIGHT), self::MEDIA_CSS_SM);
        $sliderNavigationBreakpoints .= $this->getHiddenClass(($this->getNavigationPosition() !== self::NAVIGATION_POSITION_TOP_RIGHT), self::MEDIA_CSS_XS);
        return trim($sliderNavigationBreakpoints);
    }

    /**
     * Generate navigation for slider widget
     *
     * @return string
     */
    public function getSliderNavigation()
    {
        $navigation = '<div class="swiper-navigation ' . $this->getSliderNavigationBreakpoints() . '" id="swiper-navigation-' . $this->getWidgetId() . '"><div class="swiper-button-prev"></div><div class="swiper-button-next"></div></div>';
        return $navigation;
    }

    /**
     *  Get value of slider widget navigation enable parameter
     *
     * @return string
     */
    public function getNavigationPosition()
    {
        if (!$this->hasData('navigation_position')) {
            $this->setData('navigation_position', self::DEFAULT_NAVIGATION_POSITION);
        }
        return $this->getData('navigation_position');
    }

    /**
     * Return navigation on the top tight
     *
     * @return bool|string
     */
    public function getSliderNavigationTopRight()
    {
        if ($this->getNavigationPosition() == self::NAVIGATION_POSITION_TOP_RIGHT) {
            return $this->getSliderNavigation();
        }
        return false;
    }

    /**
     * Return navigation inside slider
     *
     * @return bool|string
     */
    public function getSliderNavigationInsideSlider()
    {
        if ($this->getNavigationPosition() == self::NAVIGATION_POSITION_INSIDE_CONTAINER) {
            return $this->getSliderNavigation();
        }
        return false;
    }

    /**
     *  Get value of slider widget pagination enable parameter
     *
     * @return string
     */
    public function getPaginationEnable()
    {
        if (!$this->hasData('pagination')) {
            $this->setData('pagination', self::DEFAULT_PAGINATION_ENABLE);
        }
        return $this->stringToBoolean($this->getData('pagination'));
    }

    /**
     *  Generate pagination js option option for slider widget
     *
     * @param bool $isShow
     * @return array|bool
     */
    public function getPaginationJsOption($isShow = false)
    {
        if($this->getPaginationEnable() || $isShow){
            return [
                'el' => '#swiper-pagination-' . $this->getWidgetId(),
                'clickable' => true
            ];
        }
        return false;
    }

    /**
     * Generate breakpoints for slider widget pagination
     *
     * @return string
     */
    public function getSliderPaginationBreakpoints()
    {
        $sliderNavigationBreakpoints = $this->getHiddenClass(!$this->getPaginationEnable(), self::MEDIA_CSS_LG);
        $sliderNavigationBreakpoints .= $this->getHiddenClass(!$this->getPaginationEnable(), self::MEDIA_CSS_MD);
        $sliderNavigationBreakpoints .= $this->getHiddenClass(false, self::MEDIA_CSS_SM);
        $sliderNavigationBreakpoints .= $this->getHiddenClass(false, self::MEDIA_CSS_XS);
        return trim($sliderNavigationBreakpoints);
    }

    /**
     * Generate pagination for slider widget
     *
     * @return string
     */
    public function getSliderPagination()
    {
        $pagination = '<div class="swiper-pagination ' . $this->getSliderPaginationBreakpoints() . '" id="swiper-pagination-' . $this->getWidgetId() . '"></div>';
        return $pagination;
    }

    /**
     *  Get value of slider widget autoplay enable parameter
     *
     * @return bool
     */
    public function getAutoplayEnable()
    {
        if (!$this->hasData('autoplay')) {
            $this->setData('autoplay', self::DEFAULT_AUTOPLAY_ENABLE);
        }
        return $this->stringToBoolean($this->getData('autoplay'));
    }

    /**
     *  Get value of slider widget autoplay delay parameter
     *
     * @return string
     */
    public function getAutoplaDelay()
    {
        if (!$this->hasData('autoplay_delay')) {
            $this->setData('autoplay_delay', self::DEFAULT_AUTOPLAY_DELAY);
        }
        return $this->getData('autoplay_delay');
    }

    /**
     * Generate navigation js option option for slider widget
     *
     * @param bool $isShow
     * @return array|bool
     */
    public function getAutoplayJsOption($isShow = false)
    {
        if($this->getAutoplayEnable() || $isShow){
            return [
                'delay' => $this->getAutoplaDelay()
            ];
        }
        return false;
    }

    /**
     *  Get value of slider widget rtl enable parameter
     *
     * @return bool
     */
    public function getSliderRtlLayoutEnable()
    {
        if (!$this->hasData('slider_rtl_layout')) {
            $this->setData('slider_rtl_layout', self::DEFAULT_SLIDER_RTL_LAYOUT_ENABLE);
        }
        return $this->stringToBoolean($this->getData('slider_rtl_layout'));
    }

    /**
     *  Convert slider to rtl layout
     *
     * @return string
     */
    public function getSliderRtl()
    {
        if ($this->getSliderRtlLayoutEnable()) {
            return 'dir="rtl"';
        }
        return ;
    }

    /**
     * Composes configuration for js
     *
     * @return string
     */
    public function getJsonConfig()
    {
        $config = [
            'init' => false,
            'spaceBetween' => 0,
            'loop' => true,
            'autoplay' => $this->getAutoplayJsOption(),
            'slidesPerView' => $this->getVisibleProducts(),
            'breakpoints' => [
                '767' => [
                    'slidesPerView' => $this->getVisibleProductsMobile()
                ],
                '991' => [
                'slidesPerView' => $this->getVisibleProductsTablet()
                ]
            ],
            'pagination' => $this->getPaginationJsOption(true),
            'navigation' => $this->getNavigationJsOption(true)
        ];
        return substr($this->jsonSerializer->serialize($config), 1, -1);
    }

    /**
     * Get value of widgets' featured category id parameter
     *
     * @return string
     */
    public function getFeaturedCategoryId()
    {
        if (!$this->hasData('featured_category')) {
            $this->setData('featured_category', self::FEATURED_CATEGORY_ID);
        }
        return $this->getData('featured_category');
    }

    /**
     * Get sort collection sort by key
     *
     * @return mixed
     */
    public function getSortBy()
    {
        if (!$this->hasData('collection_sort_by')) {
            $this->setData('collection_sort_by', self::DEFAULT_COLLECTION_SORT_BY);
        }
        return $this->getData('collection_sort_by');
    }

    /**
     * Get collection sort order
     *
     * @return mixed
     */
    public function getSortOrder()
    {
        if (!$this->hasData('collection_sort_order')) {
            $this->setData('collection_sort_order', self::DEFAULT_COLLECTION_SORT_ORDER);
        }
        return $this->getData('collection_sort_order');
    }

    /**
     * Get number of current page based on query value
     *
     * @return int
     */
    public function getCurrentPage()
    {
        return abs((int)$this->getRequest()->getParam($this->getData('page_var_name')));
    }

    /**
     * Retrieve display type for products
     *
     * @return string
     */
    public function getWidgetType()
    {
        if (!$this->hasData('widget_type')) {
            $this->setData('widget_type', self::DISPLAY_TYPE_ALL_PRODUCTS);
        }
        return $this->getData('widget_type');
    }

    /**
     * Get value of widget id parameter
     *
     * @return mixed|string
     */
    public function getWidgetId()
    {
        if ($this->hasData('widget_id')) {
            return $this->getData('widget_id');
        }

        if (null === $this->getData('widget_id')) {
            $this->setData('widget_id', rand(1000, 100000));
        }

        return $this->getData('widget_id');
    }

    /**
     * Get value of widget parameter by key
     *
     * @param $value
     * @return bool
     */
    public function getOptionVal($value)
    {
        if($this->hasData($value) && $this->getData($value) == 'true') {
            return true;
        }
        return false;
    }

    /**
     *  Convert string value to boolean
     *
     * @param $value
     * @return bool
     */
    private function stringToBoolean($value){
        if ($value == '1' || $value == '0'){
            return (boolean)$value;
        }
        if ($value == 'true'){
            return true;
        }
        return false;
    }

    /**
     * Retrieve how many products should be displayed
     *
     * @return int
     */
    public function getProductsCount()
    {
        if ($this->hasData('products_count')) {
            return $this->getData('products_count');
        }

        if (null === $this->getData('products_count')) {
            $this->setData('products_count', self::DEFAULT_PRODUCTS_COUNT);
        }

        return $this->getData('products_count');
    }

    /**
     * Retrieve how many products should be displayed
     *
     * @return int
     */

    public function getProductsPerPage()
    {
        if (!$this->hasData('products_per_page')) {
            $this->setData('products_per_page', self::DEFAULT_PRODUCTS_PER_PAGE);
        }
        return $this->getData('products_per_page');
    }

    /**
     * Return flag whether pager need to be shown or not
     *
     * @return bool
     */
    public function showPager()
    {
        if (!$this->hasData('show_pager')) {
            $this->setData('show_pager', self::DEFAULT_SHOW_PAGER);
        }
        return (bool)$this->getData('show_pager');
    }

    /**
     * Retrieve how many products should be displayed on page
     *
     * @return int
     */
    protected function getPageSize()
    {
        return $this->showPager() ? $this->getProductsPerPage() : $this->getProductsCount();
    }

    /**
     * Get value of widgets' title parameter
     *
     * @return mixed|string
     */
    public function getTitle()
    {
        return $this->getData('title');
    }

    /**
     * Render pagination HTML
     *
     * @return string
     */
    public function getPagerHtml()
    {
        if ($this->showPager()) {
            if (!$this->pager) {
                $this->pager = $this->getLayout()->createBlock(
                    Pager::class,
                    'widget.new.product.list.pager'
                );
                $this->pager->setUseContainer(true)
                    ->setShowAmounts(true)
                    ->setShowPerPage(false)
                    ->setPageVarName($this->getData('page_var_name'))
                    ->setLimit($this->getProductsPerPage())
                    ->setTotalLimit($this->getProductsCount())
                    ->setCollection($this->getProductCollection());
            }
            if ($this->pager instanceof AbstractBlock) {
                return $this->pager->toHtml();
            }
        }
        return '';
    }

    /**
     * Get conditions
     *
     * @return \Magento\Rule\Model\Condition\Combine
     */
    protected function getConditions()
    {
        $conditions = $this->getData('conditions_encoded')
            ? $this->getData('conditions_encoded')
            : $this->getData('conditions');

        if ($conditions) {
            $conditions = $this->conditionsHelper->decode($conditions);
        }

        foreach ($conditions as $key => $condition) {
            if (!empty($condition['attribute'])
                && in_array($condition['attribute'], ['special_from_date', 'special_to_date'])
            ) {
                $conditions[$key]['value'] = date('Y-m-d H:i:s', strtotime($condition['value']));
            }
        }

        $this->rule->loadPost(['conditions' => $conditions]);
        return $this->rule->getConditions();
    }

    /**
     * Return HTML block with price
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param string $priceType
     * @param string $renderZone
     * @param array $arguments
     * @return string
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function getProductPriceHtml(
        Product $product,
        $priceType = null,
        $renderZone = Render::ZONE_ITEM_LIST,
        array $arguments = []
    ) {
        if (!isset($arguments['zone'])) {
            $arguments['zone'] = $renderZone;
        }
        $arguments['zone'] = isset($arguments['zone'])
            ? $arguments['zone']
            : $renderZone;
        $arguments['price_id'] = isset($arguments['price_id'])
            ? $arguments['price_id']
            : 'old-price-' . $product->getId() . '-' . $priceType;
        $arguments['include_container'] = isset($arguments['include_container'])
            ? $arguments['include_container']
            : true;
        $arguments['display_minimal_price'] = isset($arguments['display_minimal_price'])
            ? $arguments['display_minimal_price']
            : true;

            /** @var \Magento\Framework\Pricing\Render $priceRender */
        $priceRender = $this->getLayout()->getBlock('product.price.render.default');

        $price = '';
        if ($priceRender) {
            $price = $priceRender->render(
                FinalPrice::PRICE_CODE,
                $product,
                $arguments
            );
        }
        return $price;
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        return [Product::CACHE_TAG];
    }
}
