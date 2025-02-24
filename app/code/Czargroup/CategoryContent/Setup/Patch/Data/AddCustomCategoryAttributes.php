<?php

namespace Czargroup\CategoryContent\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Catalog\Model\Category;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;

class AddCustomCategoryAttributes implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;
    
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;
    
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup  = $moduleDataSetup;
        $this->eavSetupFactory  = $eavSetupFactory;
    }
    
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        
        $this->addCustomCategoryContentAttribute($eavSetup);
        $this->addCustomCategoryBannerAttribute($eavSetup);
        $this->addCustomCategoryExtraInfoAttribute($eavSetup);
        $this->addCustomCategoryCustomFieldAttribute($eavSetup);
        
        $this->moduleDataSetup->getConnection()->endSetup();
        return $this;
    }
    
    private function addCustomCategoryContentAttribute($eavSetup)
    {
        $eavSetup->addAttribute(
            Category::ENTITY,
            'category_banner_content',
            [
                'type'                    => 'text',
                'label'                   => 'Category Banner Content',
                'input'                   => 'textarea',
                'required'                => false,
                'sort_order'              => 100,
                'global'                  => ScopedAttributeInterface::SCOPE_STORE,
                'group'                   => 'Content',
                'visible'                 => true,
                'wysiwyg_enabled'         => true,
                'is_html_allowed_on_front'=> true,
            ]
        );
    }
    
    private function addCustomCategoryBannerAttribute($eavSetup)
    {
        $eavSetup->addAttribute(
            Category::ENTITY,
            'category_below_banner_content',
            [
                'type'                    => 'text',
                'label'                   => 'Category Below Banner Content',
                'input'                   => 'textarea',
                'required'                => false,
                'sort_order'              => 110,
                'global'                  => ScopedAttributeInterface::SCOPE_STORE,
                'group'                   => 'Content',
                'visible'                 => true,
                'wysiwyg_enabled'         => true,
                'is_html_allowed_on_front'=> true,
            ]
        );
    }
    
    private function addCustomCategoryExtraInfoAttribute($eavSetup)
    {
        $eavSetup->addAttribute(
            Category::ENTITY,
            'category_above_shows_content',
            [
                'type'                    => 'text',
                'label'                   => 'Category Above Shows Content',
                'input'                   => 'textarea',
                'required'                => false,
                'sort_order'              => 120,
                'global'                  => ScopedAttributeInterface::SCOPE_STORE,
                'group'                   => 'Content',
                'visible'                 => true,
                'wysiwyg_enabled'         => true,
                'is_html_allowed_on_front'=> true,
            ]
        );
    }
    
    private function addCustomCategoryCustomFieldAttribute($eavSetup)
    {
        $eavSetup->addAttribute(
            Category::ENTITY,
            'category_above_footer_content',
            [
                'type'                    => 'text',
                'label'                   => 'Category Above Footer Content',
                'input'                   => 'textarea',
                'required'                => false,
                'sort_order'              => 130,
                'global'                  => ScopedAttributeInterface::SCOPE_STORE,
                'group'                   => 'Content',
                'visible'                 => true,
                'wysiwyg_enabled'         => true,
                'is_html_allowed_on_front'=> true,
            ]
        );
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
