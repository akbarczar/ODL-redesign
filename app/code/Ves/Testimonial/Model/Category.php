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

namespace Ves\Testimonial\Model;
use Ves\Testimonial\Api\Data\CategoryInterface;
use Ves\Testimonial\Api\Data\CategoryInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;

class Category extends \Magento\Framework\Model\AbstractModel implements CategoryInterface
{
    /**
     * Testimonial's Statuses
     */
    const STATUS_ENABLED  = 1;
    const STATUS_DISABLED = 0;




    protected $_resource;


    protected $categoryDataFactory;

    protected $dataObjectHelper;

    /**
     * Category constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ResourceModel\Category|null $resource
     * @param ResourceModel\Category\Collection|null $resourceCollection
     * @param CategoryInterfaceFactory $categoryDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Ves\Testimonial\Model\ResourceModel\Category $resource = null,
        \Ves\Testimonial\Model\ResourceModel\Category\Collection $resourceCollection = null,
        CategoryInterfaceFactory $categoryDataFactory,
        DataObjectHelper $dataObjectHelper,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_resource = $resource;
        $this->categoryDataFactory = $categoryDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;

    }//end __construct()


    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Ves\Testimonial\Model\ResourceModel\Category');

    }//end _construct()

    /**
     * Retrieve category model with category data
     * @return CategoryInterface
     */
    public function getDataModel()
    {
        $categoryData = $this->getData();
        
        $categoryDataObject = $this->categoryDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $categoryDataObject,
            $categoryData,
            CategoryInterface::class
        );
        
        return $categoryDataObject;
    }

    /**
     * Prepare page's statuses.
     * Available event cms_page_get_available_statuses to customize statuses.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [
                self::STATUS_ENABLED  => __('Enabled'),
                self::STATUS_DISABLED => __('Disabled'),
               ];

    }//end getAvailableStatuses()

    /**
     * {@inheritdoc}
     */
    public function getCategoryId(){
        return $this->getData(self::CATEGORY_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCategoryId($value){
        return $this->setData(self::CATEGORY_ID, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getName(){
        return $this->getData(self::NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setName($value){
        return $this->setData(self::NAME, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getCreationTime(){
        return $this->getData(self::CREATION_TIME); 
    }

    /**
     * {@inheritdoc}
     */
    public function setCreationTime($value){
        return $this->setData(self::CREATION_TIME, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsActive(){
        return $this->getData(self::IS_ACTIVE);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsActive($value){
        return $this->setData(self::IS_ACTIVE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes()
    {
        return $this->getDataExtensionAttributes();
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(
        \Ves\Testimonial\Api\Data\CategoryExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}//end class
