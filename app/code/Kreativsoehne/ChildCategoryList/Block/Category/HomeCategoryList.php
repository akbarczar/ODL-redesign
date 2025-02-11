<?php
namespace Kreativsoehne\ChildCategoryList\Block\Category;
class HomeCategoryList extends \Magento\Framework\View\Element\Template
{

  protected $_categoryCollectionFactory;
  protected $_categoryHelper;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,        
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Catalog\Helper\Category $categoryHelper,
        array $data = []
    )
    {
        $this->_categoryCollectionFactory = $categoryCollectionFactory;
        $this->_categoryHelper = $categoryHelper;
        parent::__construct($context, $data);
    }

    public function getCategories()
    {
        $collection = $this->_categoryCollectionFactory->create();
        $categoryIds = array(434,425,1372,430,50,298,431,10,428,426,433,8);
        $collection->addAttributeToFilter('entity_id',array('in',$categoryIds));
        return $collection;
    }
  }