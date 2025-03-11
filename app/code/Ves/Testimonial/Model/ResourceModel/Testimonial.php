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

namespace Ves\Testimonial\Model\ResourceModel;

class Testimonial extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;


    /**
     * Testimonial constructor.
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param null $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        $connectionName = null
        ) {
        parent::__construct($context, $connectionName);
        $this->_storeManager = $storeManager;

    }//end __construct()


    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('ves_testimonial_testimonial', 'testimonial_id');

    }//end _construct()


    /**
     * Process block data before deleting
     *
     * @param  \Magento\Framework\Model\AbstractModel $object
     * @return \Magento\Cms\Model\ResourceModel\Page
     */
    protected function _beforeDelete(\Magento\Framework\Model\AbstractModel $object)
    {
        $condition = ['testimonial_id = ?' => (int) $object->getId()];
        $this->getConnection()->delete($this->getTable('ves_testimonial_testimonial'), $condition);
        return parent::_beforeDelete($object);

    }//end _beforeDelete()


    /**
     * Perform operations before object save
     *
     * @param  \Magento\Framework\Model\AbstractModel $object
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        return $this;

    }//end _beforeSave()


    /**
     * Perform operations after object save
     *
     * @param  \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($stores = $object->getStores()) {
            $table = $this->getTable('ves_testimonial_testimonial_store');
            $where = ['testimonial_id = ?' => (int) $object->getId()];
            $this->getConnection()->delete($table, $where);
            if ($stores) {
                $data = [];
                foreach ($stores as $storeId) {
                    $data[] = [
                    'testimonial_id' => (int) $object->getId(),
                    'store_id'       => (int) $storeId,
                    ];
                }

                $this->getConnection()->insertMultiple($table, $data);
            }
        }

        if ($category = $object->getCategories()) {
            $table = $this->getTable('ves_testimonial_testimonial_category');
            $where = ['testimonial_id = ?' => (int) $object->getId()];
            $this->getConnection()->delete($table, $where);

            if ($category) {
                $data = [];
                foreach ($category as $categoryId) {
                    $data[] = [
                    'testimonial_id' => (int) $object->getId(),
                    'category_id'    => (int) $categoryId,
                    ];
                }

                $this->getConnection()->insertMultiple($table, $data);
            }
        }

        // Testimonial Product
        $table = $this->getTable('ves_testimonial_testimonial_product');
        $where = ['testimonial_id = ?' => (int) $object->getId()];
        if ($testimonialProducts = $object->getData('testimonial_products')) {
            $this->getConnection()->delete($table, $where);
            $where = ['testimonial_id = ?' => (int) $object->getId()];
            $this->getConnection()->delete($table, $where);
            $data = [];
            foreach ($testimonialProducts as $k => $_post) {
                $data[] = [
                'testimonial_id' => (int) $object->getId(),
                'product_id'     => $k,
                'position'       => $_post['position'],
                ];
            }

            $this->getConnection()->insertMultiple($table, $data);
        }

        return parent::_afterSave($object);

    }//end _afterSave()


    /**
     * Perform operations after object load
     *
     * @param  \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->getId()) {
            $stores = $this->lookupStoreIds($object->getId());
            $object->setData('store_id', $stores);
            $object->setData('stores', $stores);
        }

        if ($object->getId()) {
            $categories = $this->lookupCategoryIds($object->getId());
            $object->setData('category_id', $categories);
            $object->setData('categories', $categories);
        }

        if ($id = $object->getId()) {
            $connection = $this->getConnection();
            $select     = $connection->select()
            ->from($this->getTable('ves_testimonial_testimonial_product'))
            ->where(
                'testimonial_id = '.(int) $id
                );
            $products   = $connection->fetchAll($select);
            $object->setData('testimonial_products', $products);
            if($products){
                $new_products = [];
                foreach($products as $_product){
                    $new_products[] = (int)$_product['product_id'];
                }
                $object->setData('products', $new_products);
            }
        }

        if ($id = $object->getId()) {
            $connection = $this->getConnection();
            $select     = $connection->select()
            ->from($this->getTable('ves_testimonial_testimonial'))
            ->where(
                'testimonial_id = '.(int) $id
                );
            $posts      = $connection->fetchAll($select);
            $object->setData('posts', $posts);
        }

        return parent::_afterLoad($object);

    }//end _afterLoad()


    /**
     * Retrieve select object for load object data
     *
     * @param  string                   $field
     * @param  mixed                    $value
     * @param  \Magento\Cms\Model\Block $object
     * @return \Magento\Framework\DB\Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);

        if ($object->getStoreId()) {
            $stores = [
            (int) $object->getStoreId(),
            \Magento\Store\Model\Store::DEFAULT_STORE_ID,
            ];

            $select->join(
                ['cbs' => $this->getTable('ves_testimonial_testimonial_store')],
                $this->getMainTable().'.testimonial_id = cbs.testimonial_id',
                ['store_id']
                )->where(
                'is_active = ?',
                1
                )->where(
                'cbs.store_id in (?)',
                $stores
                )->order(
                'store_id DESC'
                )->limit(
                1
                );
        }//end if

        return $select;

    }//end _getLoadSelect()


    /**
     * Check for unique of identifier of block to selected store(s).
     *
     * @param                                        \Magento\Framework\Model\AbstractModel $object
     * @return                                       bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsUniqueBlockToStores(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($this->_storeManager->hasSingleStore()) {
            $stores = [\Magento\Store\Model\Store::DEFAULT_STORE_ID];
        } else {
            $stores = (array) $object->getData('stores');
        }

        $select = $this->getConnection()->select()->from(
            ['cb' => $this->getMainTable()]
            )->join(
            ['cbs' => $this->getTable('ves_testimonial_testimonial_store')],
            'cb.testimonial_id = cbs.testimonial_id',
            []
            )->where(
            'cb.identifier = ?',
            $object->getData('identifier')
            )->where(
            'cbs.store_id IN (?)',
            $stores
            );

            if ($object->getId()) {
                $select->where('cb.testimonial_id <> ?', $object->getId());
            }

            if ($this->getConnection()->fetchRow($select)) {
                return false;
            }

            return true;

    }//end getIsUniqueBlockToStores()


    /**
     * Get store ids to which specified item is assigned
     *
     * @param  int $id
     * @return array
     */
    public function lookupStoreIds($id)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from(
            $this->getTable('ves_testimonial_testimonial_store'),
            'store_id'
            )->where(
            'testimonial_id = :testimonial_id'
            );

            $binds = [':testimonial_id' => (int) $id];

            return $connection->fetchCol($select, $binds);

    }//end lookupStoreIds()


    public function lookupCategoryIds($id)
    {
        $connection = $this->getConnection();
        $select     = $connection->select()->from(
            $this->getTable('ves_testimonial_testimonial_category'),
            'category_id'
            )->where(
            'testimonial_id = :testimonial_id'
            );
            $binds      = [':testimonial_id' => (int) $id];
            return $connection->fetchCol($select, $binds);

    }//end lookupCategoryIds()


}//end class
