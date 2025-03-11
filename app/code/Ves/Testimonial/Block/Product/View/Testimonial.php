<?php
/**
 * Venustheme
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://venustheme.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category  Venustheme
 * @package   Ves_Testimonial
 * @copyright Copyright (c) 2017 Landofcoder (http://www.venustheme.com/)
 * @license   http://www.venustheme.com/LICENSE-1.0.html
 */

namespace Ves\Testimonial\Block\Product\View;

class Testimonial extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
    /**
     * Name of request parameter for page number value
     */
    const PAGE_VAR_NAME = 'ntmp';
    /**
     * Instance of pager block
     *
     * @var \Magento\Catalog\Block\Product\Widget\Html\Pager
     */
    protected $_pager;
    /**
     * @var \Ves\Testimonial\Helper\Data
     */
    protected $_helper;
    /**
     * @var \Ves\Testimonial\Model\ResourceModel\Testimonial\CollectionFactory
     */
    protected $_testimonialCollectionFactory;
     /**
      * @var \PHPUnit_Framework_MockObject_MockObject
      */
    protected $_resource;

    protected $_registry;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Ves\Testimonial\Helper\Data $_helper,
        \Ves\Testimonial\Model\ResourceModel\Testimonial\CollectionFactory $testimonialCollectionFactory,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_helper = $_helper;
        $this->_testimonialCollectionFactory = $testimonialCollectionFactory;
        $this->_resource = $resource;
        $this->_registry = $registry;
        parent::__construct($context);

    }//end __construct()


    public function _toHtml()
    {
        $enable = $this->_helper->getConfig('general/enable');
        if(!$enable) { return;
        }

        $enable_product = $this->_helper->getConfig('testimonial_productpage/enable_product');
        if(!$enable_product) { return;
        }

        $template = '';
        $layout   = $this->_helper->getConfig('testimonial_productpage/layout');
        switch ($layout) {
        case 'topmeta':
            $template = 'Ves_Testimonial::product/topmeta.phtml';
            break;
        case 'bottommeta':
            $template = 'Ves_Testimonial::product/bottommeta.phtml';
            break;
        case 'alltop':
            $template = 'Ves_Testimonial::product/alltop.phtml';
            break;
        case 'allbottom':
            $template = 'Ves_Testimonial::product/allbottom.phtml';
            break;
        case 'topimage':
            $template = 'Ves_Testimonial::product/topimage.phtml';
            break;
        case 'bottomimage':
            $template = 'Ves_Testimonial::product/bottomimage.phtml';
            break;
        case 'style1':
            $template = 'Ves_Testimonial::product/style1.phtml';
            break;
        case 'style2':
            $template = 'Ves_Testimonial::product/style2.phtml';
            break;
        case 'style3':
            $template = 'Ves_Testimonial::product/style3.phtml';
            break;
        case 'style4':
            $template = 'Ves_Testimonial::product/style4.phtml';
            break;
        case 'slide1':
            $template = 'Ves_Testimonial::product/slide1.phtml';
            break;
        case 'slide2':
            $template = 'Ves_Testimonial::product/slide2.phtml';
            break;
        case 'grid':
            $template = 'Ves_Testimonial::product/grid.phtml';
            break;
        case 'grid1':
            $template = 'Ves_Testimonial::product/grid1.phtml';
            break;
        case 'grid2':
            $template = 'Ves_Testimonial::product/grid2.phtml';
            break;
        case 'list':
            $template = 'Ves_Testimonial::product/list.phtml';
            break;
        }//end switch

        if($blockTemplate = $this->_helper->getConfig('testimonial_productpage/block_template')) {
            $template = $blockTemplate;
        }

        $this->setTemplate($template);
        $orderBy       = $this->_helper->getConfig('testimonial_productpage/order_by');
        $category      = $this->_helper->getConfig('testimonial_productpage/category');
        $cats          = explode(',', $category);
        $item_per_page = (int) $this->_helper->getConfig('testimonial_productpage/item_per_page');
        if(!$this->_helper->getConfig('testimonial_productpage/grid_pagination') || $layout != 'grid' || $layout != 'grid1' || $layout != 'grid2') {
            $item_per_page = $this->_helper->getConfig('testimonial_productpage/number_item');
        }

        $store = $this->_storeManager->getStore();
        $product_id = 0;
        if($current_product = $this->_registry->registry('current_product')){
            $product_id = $current_product->getId();
        }
        $testimonialCollection = $this->_testimonialCollectionFactory->create()
            ->setPageSize($item_per_page)
            ->addStoreFilter($store)
            ->addProductFilter($product_id)
            ->addFieldToFilter('is_active', '1');
        if(count($cats) > 0) {
            $testimonialCollection->getSelect("main_table.testimonial_id")
                ->joinLeft(
                    [
                     'cat' => $this->_resource->getTableName('ves_testimonial_testimonial_category'),
                    ],
                    'cat.testimonial_id = main_table.testimonial_id',
                    ["testimonial_id" => "testimonial_id"]
                )->where('cat.category_id IN (?)', implode($cats, ','));
            $testimonialCollection->getSelect()->order("main_table.testimonial_id DESC")->group('cat.testimonial_id');
        }

        if($orderBy == 'rating') {
            $testimonialCollection->setOrder('rating', 'DESC');
        }else if($orderBy == 'position') {
            $testimonialCollection->setOrder('position', 'ASC');
        }else if($orderBy == 'random') {
            $testimonialCollection->getSelect()->order('rand()');
        }else if($orderBy == 'recent') {
            $testimonialCollection->setOrder('main_table.create_time', 'DESC');
        }

        if($this->_helper->getConfig('testimonial_productpage/grid_pagination') || $layout == 'grid' || $layout == 'grid1' || $layout == 'grid2') {
            $currentPage = $this->getCurrentPage();
            $testimonialCollection->setCurPage($currentPage);
        }

       

        $this->setTestimonialCollection($testimonialCollection);
        return parent::_toHtml();

    }//end _toHtml()


    /**
     * Get number of current page based on query value
     *
     * @return int
     */
    public function getCurrentPage()
    {
        return abs((int) $this->getRequest()->getParam(self::PAGE_VAR_NAME));

    }//end getCurrentPage()


    /**
     * Render pagination HTML
     *
     * @return string
     */
    public function getPagerHtml()
    {
        $numberItem    = (int) $this->_helper->getConfig('testimonial_productpage/number_item');
        $item_per_page = (int) $this->_helper->getConfig('testimonial_productpage/item_per_page');
        $name          = 'ves.testimonial.product'.time().uniqid();
        if (!$this->_pager) {
            $this->_pager = $this->getLayout()->createBlock(
                'Magento\Catalog\Block\Product\Widget\Html\Pager',
                $name
            );
            $this->_pager->setUseContainer(true)
                ->setShowAmounts(false)
                ->setShowPerPage(false)
                ->setPageVarName(self::PAGE_VAR_NAME)
                ->setLimit($item_per_page)
                ->setTotalLimit($numberItem)
                ->setCollection($this->getTestimonialCollection());
        }

        if ($this->_pager instanceof \Magento\Framework\View\Element\AbstractBlock) {
            return $this->_pager->toHtml();
        }

    }//end getPagerHtml()


    public function setTestimonialCollection($collection)
    {
        $this->_collection = $collection;
        return $this;

    }//end setTestimonialCollection()


    public function getTestimonialCollection()
    {
        return $this->_collection;

    }//end getTestimonialCollection()


    public function getConfig($key, $default = '')
    {
        if($this->hasData($key) && $this->getData($key)) {
            return $this->getData($key);
        }

        return $default;

    }//end getConfig()


}//end class
