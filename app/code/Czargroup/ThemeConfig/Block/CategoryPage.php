<?php

namespace Czargroup\ThemeConfig\Block;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Block\Product\ListProduct;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\View\Element\Template;
use Magento\Sales\Model\ResourceModel\Report\Bestsellers\CollectionFactory as BestsellerCollectionFactory;
use Magento\Catalog\Helper\Image;
use Magento\Catalog\Block\Category\View as CategoryView;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use Magento\Catalog\Helper\Category as CategoryHelper;
class CategoryPage extends Template
{
    protected $categoryRepository;
    protected $productCollectionFactory;
    protected $bestsellerCollectionFactory;
    protected $imageHelper;
      /**
     * @var FilterProvider
     */
    protected $filterProvider;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param LayerResolver $layerResolver
     * @param \Magento\Framework\Registry $registry
     * @param FilterProvider $filterProvider
     * @param array $data
     */

     public function __construct(
        Template\Context $context,
        CategoryRepositoryInterface $categoryRepository,
        CollectionFactory $productCollectionFactory,
        BestsellerCollectionFactory $bestsellerCollectionFactory,
        Image $imageHelper,
        LayerResolver $layerResolver,
        FilterProvider $filterProvider,
        \Magento\Framework\Registry $registry,
        CategoryHelper $categoryHelper,
        array $data = []
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->bestsellerCollectionFactory = $bestsellerCollectionFactory;
        $this->imageHelper = $imageHelper;
        $this->filterProvider = $filterProvider;
        parent::__construct($context, $data);
    }

    public function getCurrentCategory()
    {
        $categoryId = $this->getRequest()->getParam('id');
        if ($categoryId) {
            return $this->categoryRepository->get($categoryId);
        }
        return null;
    }

    public function getBestsellerProducts($categoryId, $limit = 12)
    {
        $productCollection = $this->productCollectionFactory->create();
        $productCollection->addAttributeToSelect('*')
            ->addCategoriesFilter(['in' => $categoryId])
            ->addAttributeToFilter('visibility', ['neq' => 1]) // Exclude not visible individually
            ->setPageSize($limit);

        $productCollection->getSelect()->joinLeft(
            ['sales_order_item' => $productCollection->getTable('sales_order_item')],
            'e.entity_id = sales_order_item.product_id',
            ['ordered_qty' => 'SUM(sales_order_item.qty_ordered)']
        )->group('e.entity_id')->order('ordered_qty DESC');

        return $productCollection;
    }
    public function getProductImageUrl($product, $imageType = 'category_page_grid')
    {
        return $this->imageHelper->init($product, $imageType)->getUrl();
    }
     /**
     * Retrieve filtered content from the category attribute.
     *
     * @param string $attributeCode
     * @return string
     */
    public function getFilteredContent($attributeCode)
    {
        $category = $this->getCurrentCategory();
        $categorydata = $category->getData($attributeCode);
        if (!$categorydata) {
            return '';
        }
        return $this->filterProvider->getPageFilter()->filter($categorydata);
    }
}
