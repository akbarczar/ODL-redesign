<?php
namespace Mexbs\Fbshop\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetupFactory;

class InstallData implements InstallDataInterface
{
    protected  $eavSetupFactory;
    protected  $attributesMappingFactory;
    protected  $productAttributeCollectionFactory;
    protected  $productActionFactory;
    protected  $productCollectionFactory;


    public function __construct(
        EavSetupFactory $eavSetupFactory,
        \Mexbs\Fbshop\Model\AttributesMappingFactory $attributesMappingFactory,
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $productAttributeCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\ProductFactory $productResourceFactory,
        \Magento\Catalog\Model\ResourceModel\Product\ActionFactory $productActionFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
    )
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->attributesMappingFactory = $attributesMappingFactory;
        $this->productAttributeCollectionFactory = $productAttributeCollectionFactory;
        $this->productActionFactory = $productActionFactory;
        $this->productCollectionFactory = $productCollectionFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        /**
         * @var \Magento\Eav\Setup\EavSetup $eavSetup
         */
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'is_in_fb_feed',
            [
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Is Product in Facebook Feed',
                'input' => 'boolean',
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'class' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'visible' => false,
                'required' => false,
                'user_defined' => false,
                'default' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::VALUE_NO,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => true,
                'is_filterable_in_grid' => true,
                'unique' => false,
                'apply_to' => ''
            ]
        );

        $defaultMappings = [
            'title' => 'name'
        ];

        foreach($defaultMappings as $fbFieldName => $attributeCode){
            /**
             * @var \Mexbs\Fbshop\Model\AttributesMapping $attributesMapping
             */
            $attributesMapping = $this->attributesMappingFactory->create();
            $attributesMapping->setFbApiFieldName($fbFieldName)
                ->setAttributeCode($attributeCode)
                ->save();
        }

        /**
         * @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection $productAttributeCollection
         */
        $productAttributeCollection = $this->productAttributeCollectionFactory->create();
        foreach($productAttributeCollection as $productAttribute){
            if(in_array($productAttribute->getAttributeCode(), \Mexbs\Fbshop\Helper\Data::$fields)
                && !in_array($productAttribute->getAttributeCode(), \Mexbs\Fbshop\Helper\Data::$defaultCompletedBySystemFields)){
                /**
                 * @var \Mexbs\Fbshop\Model\AttributesMapping $attributeMapping
                 */
                $attributeMapping = $this->attributesMappingFactory->create();
                $attributeMapping->setFbApiFieldName($productAttribute->getAttributeCode())
                    ->setAttributeCode($productAttribute->getAttributeCode())
                    ->save();
            }
        }

        /**
         * @var \Magento\Catalog\Model\ResourceModel\Product\Collection $productsCollection
         */
        $productsCollection = $this->productCollectionFactory->create();
        $productIds = [];
        foreach($productsCollection as $product){
            $productIds[] = $product->getId();
        }
        $this->productActionFactory->create()
            ->updateAttributes($productIds, ['is_in_fb_feed' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::VALUE_NO], \Magento\Store\Model\Store::DEFAULT_STORE_ID);
    }
}