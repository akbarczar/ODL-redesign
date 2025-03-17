<?php
namespace Mexbs\Fbshop\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        if (version_compare($context->getVersion(), '1.0.4', '<')) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('fbshop_custom_options_mapping')
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
                    'custom_option_title',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    '255',
                    [],
                    'Custom Option Title'
                )->setComment(
                    'FB Custom Options Mapping'
                );

            $installer->getConnection()->createTable($table);
        }

        if (version_compare($context->getVersion(), '1.0.10', '<')) {
            $setup->getConnection()->addColumn(
                $installer->getTable('sales_order'),
                'is_from_fb',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                    'nullable' => true,
                    'default' => 0,
                    'comment' => 'Did the customer come from Facebook'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.1.11', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('sales_order_grid'),
                'is_from_fb',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'comment' => 'Did the customer come from Facebook'
                ]
            );
        }

        $installer->endSetup();
    }
}
