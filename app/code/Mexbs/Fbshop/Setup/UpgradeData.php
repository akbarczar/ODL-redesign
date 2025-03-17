<?php
namespace Mexbs\Fbshop\Setup;

use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;



class UpgradeData implements UpgradeDataInterface
{
    private $categorySetupFactory;
    private $productCollectionFactory;
    private $productActionFactory;
    private $attributesMappingFactory;
    private $eavConfig;

    public function __construct(
        \Magento\Catalog\Setup\CategorySetupFactory $categorySetupFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product\ActionFactory $productActionFactory,
        \Mexbs\Fbshop\Model\AttributesMappingFactory $attributesMappingFactory,
        \Magento\Eav\Model\Config $eavConfig
    )
    {
        $this->categorySetupFactory = $categorySetupFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productActionFactory = $productActionFactory;
        $this->attributesMappingFactory = $attributesMappingFactory;
        $this->eavConfig = $eavConfig;
    }


    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '1.0.3') < 0
        ) {
            /** @var \Magento\Catalog\Setup\CategorySetup $categorySetup */
            $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);
//            $categorySetup->removeAttribute(ProductAttributeInterface::ENTITY_TYPE_CODE, 'is_fb_redirects_to_checkout');
            $categorySetup->addAttribute(
                ProductAttributeInterface::ENTITY_TYPE_CODE,
                'is_fb_redirects_to_checkout',
                [
                    'type' => 'int',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Should Redirect to Checkout from Facebook?',
                    'input' => 'boolean',
                    'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                    'class' => '',
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => true,
                    'default' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::VALUE_YES,
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

            $this->eavConfig->clear();

            /** @var \Magento\Catalog\Setup\CategorySetup $categorySetup */
            $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);
            $attributeSetIds = $categorySetup->getAllAttributeSetIds(ProductAttributeInterface::ENTITY_TYPE_CODE);

            foreach($attributeSetIds as $attributeSetId){
                $categorySetup->addAttributeToGroup(
                    ProductAttributeInterface::ENTITY_TYPE_CODE,
                    $attributeSetId,
                    'Product Details',
                    'is_fb_redirects_to_checkout',
                    200
                );
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
                ->updateAttributes($productIds, ['is_fb_redirects_to_checkout' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::VALUE_YES], \Magento\Store\Model\Store::DEFAULT_STORE_ID);

            $setup->endSetup();
        }

        if (version_compare($context->getVersion(), '1.0.4') < 0
        ) {
            /** @var \Magento\Catalog\Setup\CategorySetup $categorySetup */
            $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);
//            $categorySetup->removeAttribute(ProductAttributeInterface::ENTITY_TYPE_CODE, 'is_fb_redirects_to_checkout');
            $categorySetup->addAttribute(
                ProductAttributeInterface::ENTITY_TYPE_CODE,
                'is_resize_main_image_for_fb',
                [
                    'type' => 'int',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Should Resize the Main Image for Facebook?',
                    'input' => 'boolean',
                    'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                    'class' => '',
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => true,
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

            $this->eavConfig->clear();

            /** @var \Magento\Catalog\Setup\CategorySetup $categorySetup */
            $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);
            $attributeSetIds = $categorySetup->getAllAttributeSetIds(ProductAttributeInterface::ENTITY_TYPE_CODE);

            foreach($attributeSetIds as $attributeSetId){
                $categorySetup->addAttributeToGroup(
                    ProductAttributeInterface::ENTITY_TYPE_CODE,
                    $attributeSetId,
                    'Product Details',
                    'is_resize_main_image_for_fb',
                    220
                );
            }
            $setup->endSetup();
        }

        if (version_compare($context->getVersion(), '1.1.0') < 0
        ) {
            /**
             * @var \Mexbs\Fbshop\Model\AttributesMapping $attributeMapping
             */
            $attributeMapping = $this->attributesMappingFactory->create();
            $attributeMapping->setFbApiFieldName('rich_text_description')
                ->setAttributeCode('description')
                ->save();

        }
    }

}
