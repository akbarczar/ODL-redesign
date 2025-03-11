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

namespace Ves\Testimonial\Block\Widget;
use Magento\Customer\Model\Session as CustomerSession;

class CustomWidget extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
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
      * @var \PHPUnit_Framework_MockObject_MockObject
      */
    protected $_resource;
    /**
     * @var \Ves\Testimonial\Model\ResourceModel\Testimonial\CollectionFactory
     */
    protected $_testimonialCollectionFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Ves\Testimonial\Model\ResourceModel\Testimonial\CollectionFactory $testimonialCollectionFactory,
        \Ves\Testimonial\Helper\Data $_helper,
        \Magento\Framework\App\ResourceConnection $resource,
        CustomerSession $customerSession,
        array $data = []
    ) {
        $this->_helper   = $_helper;
        $this->urlHelper = $urlHelper;
        $this->_testimonialCollectionFactory = $testimonialCollectionFactory;
        $this->_resource = $resource;
        $this->customerSession = $customerSession;
        parent::__construct($context, $data);

    }//end __construct()


    public function _toHtml()
    {

        $enable = $this->_helper->getConfig('general/enable');
        if(!$enable) { return;
        }

        $template = '';
        $layout   = $this->getConfig('layout');
        switch ($layout) {
        case 'topmeta':
            $template = 'Ves_Testimonial::widget/topmeta.phtml';
            break;
        case 'bottommeta':
            $template = 'Ves_Testimonial::widget/bottommeta.phtml';
            break;
        case 'alltop':
            $template = 'Ves_Testimonial::widget/alltop.phtml';
            break;
        case 'allbottom':
            $template = 'Ves_Testimonial::widget/allbottom.phtml';
            break;
        case 'topimage':
            $template = 'Ves_Testimonial::widget/topimage.phtml';
            break;
        case 'bottomimage':
            $template = 'Ves_Testimonial::widget/bottomimage.phtml';
            break;
        case 'style1':
            $template = 'Ves_Testimonial::widget/style1.phtml';
            break;
        case 'style2':
            $template = 'Ves_Testimonial::widget/style2.phtml';
            break;
        case 'style3':
            $template = 'Ves_Testimonial::widget/style3.phtml';
            break;
        case 'style4':
            $template = 'Ves_Testimonial::widget/style4.phtml';
            break;
        case 'slide1':
            $template = 'Ves_Testimonial::widget/slide1.phtml';
            break;
        case 'slide2':
            $template = 'Ves_Testimonial::widget/slide2.phtml';
            break;
        case 'grid':
            $template = 'Ves_Testimonial::widget/grid.phtml';
            break;
        case 'grid1':
            $template = 'Ves_Testimonial::widget/grid1.phtml';
            break;
        case 'grid2':
            $template = 'Ves_Testimonial::widget/grid2.phtml';
            break;
        case 'list':
            $template = 'Ves_Testimonial::widget/list.phtml';
            break;
        }//end switch

        if($blockTemplate = $this->getConfig('block_template')) {
            $template = $blockTemplate;
        }

        $this->setTemplate($template);
        $orderBy  = $this->getConfig('order_by');
        $category = $this->getConfig('category');

        $cats          = explode(',', $category);
        $item_per_page = (int) $this->getConfig('item_per_page');
        $store = $this->_storeManager->getStore();
        $testimonialCollection = $this->_testimonialCollectionFactory->create()
            ->setPageSize($item_per_page)
            ->addStoreFilter($store)
            ->addFieldToFilter('is_active', '1');
        if(count($cats) > 0 && $cats[0]) {
            $testimonialCollection->getSelect("main_table.testimonial_id")
                ->joinLeft(
                    [
                     'cat' => $this->_resource->getTableName('ves_testimonial_testimonial_category'),
                    ],
                    'cat.testimonial_id = main_table.testimonial_id',
                    ["testimonial_id" => "testimonial_id"]
                )->where('cat.category_id IN (?)', $cats);
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

        if($this->getConfig('grid_pagination') || $layout == 'grid' || $layout == 'grid1' || $layout == 'grid2' || $layout == 'list') {
            $currentPage = $this->getCurrentPage();
            $testimonialCollection->setCurPage($currentPage);
        } else {
            $numberItem    = (int) $this->getConfig('number_item');
            $testimonialCollection->setPageSize($numberItem);
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
        $numberItem    = (int) $this->getConfig('number_item');
        $item_per_page = (int) $this->getConfig('item_per_page');
        $name          = 'ves.testimonial.widget'.time().uniqid();
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

    public function getCustomerInfo(){
        if($this->customerSession->getCustomerGroupId() || $this->customerSession->isLoggedIn()){
            return $this->customerSession->getCustomer();
        }
        return false;
    }

}//end class
