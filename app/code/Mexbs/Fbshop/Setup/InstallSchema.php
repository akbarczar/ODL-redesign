<?php
namespace Mexbs\Fbshop\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;


class InstallSchema implements InstallSchemaInterface
{

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $table = $installer->getConnection()->newTable(
            $installer->getTable('fbshop_attributes_mapping')
        )->addColumn(
                'mapping_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Mapping Id'
            )->addColumn(
                'fb_api_field_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '255',
                [],
                'FB Api Name'
            )->addColumn(
                'attribute_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '255',
                [],
                'Attribute Code'
            )->setComment(
                'FB Attribute Mapping'
            );

        $installer->getConnection()->createTable($table);


        $table = $installer->getConnection()->newTable(
            $installer->getTable('fbshop_log')
        )->addColumn(
                'log_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Log Id'
            )->addColumn(
                'store_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '32',
                [],
                'Store Code'
            )->addColumn(
                'triggered_by',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '40',
                [],
                'Triggered By'
            )->addColumn(
                'product_ids',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '100K',
                [],
                'Message'
            )->addColumn(
                'message',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '1K',
                [],
                'Message'
            )->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '254',
                [],
                'Status'
            )->addColumn(
                'started_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Started at'
            )->addColumn(
                'finished_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => true, 'default' => null],
                'Finished at'
            )->setComment(
                'Logs'
            );

        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('fbshop_schedule_store')
        )->addColumn(
                'schedule_store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )->addColumn(
                'schedule_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                '10',
                ['unsigned' => true, 'nullable' => false],
                'Schedule ID'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                '5',
                ['unsigned' => true, 'nullable' => false],
                'Store ID'
            )->setComment(
                'Cron Schedule to Store'
            )->addForeignKey(
                $installer->getFkName('fbshop_schedule_store', 'schedule_id', 'cron_schedule', 'schedule_id'),
                'schedule_id',
                $installer->getTable('cron_schedule'),
                'schedule_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->addForeignKey(
                $installer->getFkName('fbshop_schedule_store', 'store_id', 'store', 'store_id'),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );

        $installer->getConnection()->createTable($table);

        $installer->endSetup();

    }
}