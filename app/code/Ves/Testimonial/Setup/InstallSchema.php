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

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;


class InstallSchema implements InstallSchemaInterface
{


    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $setup->getConnection()->dropTable($setup->getTable('ves_testimonial_testimonial'));
        $setup->getConnection()->dropTable($setup->getTable('ves_testimonial_testimonial_store'));
        $table = $installer->getConnection()->newTable(
            $installer->getTable('ves_testimonial_testimonial')
        )
        ->addColumn(
            'testimonial_id',
            Table::TYPE_INTEGER,
            null,
            [
             'identity' => true,
             'nullable' => false,
             'primary'  => true,
            ],
            'Testimonial ID'
        )
        ->addColumn(
            'name',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'NickName'
        )
        ->addColumn(
            'email',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Email'
        )
        ->addColumn(
            'image',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Image'
        )
        ->addColumn(
            'company_address',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Company Address'
        )
        ->addColumn(
            'company_name',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Company Name'
        )
        ->addColumn(
            'company_website',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Company Website'
        )
        ->addColumn(
            'position',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Position'
        )
        ->addColumn(
            'linkedin',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Linkedin'
        )
        ->addColumn(
            'facebook',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Facebook'
        )
        ->addColumn(
            'twitter',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Twitter'
        )
        ->addColumn(
            'youtube',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Youtube'
        )
        ->addColumn(
            'vimeo',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Vimeo'
        )
        ->addColumn(
            'googleplus',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Google Plus'
        )
        ->addColumn(
            'address',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Address'
        )
        ->addColumn(
            'testimonial',
            Table::TYPE_TEXT,
            '2M',
            ['nullable' => false],
            'Testimonial'
        )
        ->addColumn(
            'create_time',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false],
            'Create Time'
        )
        ->addColumn(
            'rating',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false],
            'Rating'
        )
        ->addColumn(
            'job',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Job'
        )
        ->addColumn(
            'is_active',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false],
            'Number Column on Tablets'
        )
        ->addColumn(
            'title',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Title'
        )
        ->addIndex(
            $setup->getIdxName(
                $installer->getTable('ves_testimonial_testimonial'),
                [
                    'name',
                    'testimonial',
                    'email'
                ],
                AdapterInterface::INDEX_TYPE_FULLTEXT
            ),
            [
             'name',
             'testimonial',
             'email',
            ],
            ['type' => AdapterInterface::INDEX_TYPE_FULLTEXT]
        )
        ->setComment(
            'Testimonial - Testimonial Table'
        );
        $installer->getConnection()->createTable($table);

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
            'create_time',
            Table::TYPE_TIMESTAMP,
            null,
            [
                'nullable' => false
            ],
            'Create Time'
        )->addColumn(
            'is_active',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false],
            'Number Column on Tablets'
        );

        /*
        * Create table 'ves_testimonial_store'
        */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('ves_testimonial_testimonial_store')
        )->addColumn(
            'testimonial_id',
            Table::TYPE_INTEGER,
            null,
            [
                'nullable' => false,
                'primary'  => true
            ],
            'Testimonial ID'
        )->addColumn(
            'store_id',
            Table::TYPE_SMALLINT,
            null,
            [
                'nullable' => false,
                'primary'  => true
            ],
            'Store ID'
        )->addIndex(
            $installer->getIdxName('ves_testimonial_testimonial_store', ['store_id']),
            ['store_id']
        )->setComment(
            'Testimonial Store'
        );
        $installer->getConnection()->createTable($table);

    }//end install()


}//end class
