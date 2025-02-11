<?php
namespace Czargroup\ThemeConfig\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Catalog\Model\Category;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class AddCategoryImages implements DataPatchInterface
{
    private $moduleDataSetup;
    private $eavSetupFactory;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $fields = [
            'category_image_left' => 'Left Menu Image',
            'category_image_right' => 'Right Menu Image',
            'category_image_bottom' => 'Bottom Menu Image'
        ];

        foreach ($fields as $code => $label) {
            $eavSetup->addAttribute(
                Category::ENTITY,
                $code,
                [
                    'type'         => 'varchar',
                    'label'        => $label,
                    'input'        => 'image',
                    'backend'      => 'Magento\Catalog\Model\Category\Attribute\Backend\Image',
                    'required'     => false,
                    'global'       => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'visible'      => true,
                    'group'        => 'General Information'
                ]
            );
        }

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }
}
