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

namespace Ves\Testimonial\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{


    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        /*
            * Change column table 'ves_testimonial_category'
         */
         $tableName = $installer->getTable('ves_testimonial_testimonial');

        if ($installer->getConnection()->isTableExists($tableName) == true) {
            $connection = $installer->getConnection();
            $connection->changeColumn(
                $tableName,
                'name',
                'nick_name',
                [
                 'type'     => Table::TYPE_TEXT,
                 'nullable' => false,
                 'default'  => '',
                ],
                'Nick Name'
            );
        }

        $installer->endSetup();

         /*
             * Create table 'ves_testimonial_category'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('ves_testimonial_category')
        )->addColumn(
            'category_id',
            Table::TYPE_SMALLINT,
            null,
            [
                'identity' => true,
                'nullable' => false,
                'primary'  => true
            ],
            'Category ID'
        )->addColumn(
            'name',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Name'
        )->addColumn(
            'create_time',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false],
            'Create Time'
        )->addColumn(
            'is_active',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false],
            'Number Column on Tablets'
        );
        $installer->getConnection()->createTable($table);
        $installer->endSetup();

        /*
            * Create table 'ves_testimonial_testimonil_product'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('ves_testimonial_testimonial_category')
        )->addColumn(
            'testimonial_id',
            Table::TYPE_INTEGER,
            null,
            [
                'nullable' => false,
                'primary'  => true
            ],
            ' ID'
        )->addColumn(
            'category_id',
            Table::TYPE_SMALLINT,
            null,
            [
                'nullable' => false,
                'primary'  => true
            ],
            'Category ID'
        )->setComment(
            'Ves Testimonial Category Table'
        );
        $installer->getConnection()->createTable($table);
        $installer->endSetup();

        /*
            * Create table 'ves_testimonial_testimonil_product'
         */
        $setup->getConnection()->dropTable($setup->getTable('ves_testimonial_testimonial_product'));
        $table = $installer->getConnection()->newTable(
            $installer->getTable('ves_testimonial_testimonial_product')
        )->addColumn(
            'testimonial_product_id',
            Table::TYPE_INTEGER,
            null,
            [
                'identity' => true,
                'nullable' => false,
                'primary'  => true
            ],
            ' ID'
        )->addColumn(
            'testimonial_id',
            Table::TYPE_INTEGER,
            null,
            [
                'nullable' => false
            ],
            'Testimonial ID'
        )->addColumn(
            'product_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'Product ID'
        )->addColumn(
            'position',
            Table::TYPE_INTEGER,
            11,
            ['nullable' => true],
            'Position'
        )->setComment(
            'Ves Testimonial To Product Linkage Table'
        );
        $installer->getConnection()->createTable($table);
        $installer->endSetup();

    }//end upgrade()


}//end class
