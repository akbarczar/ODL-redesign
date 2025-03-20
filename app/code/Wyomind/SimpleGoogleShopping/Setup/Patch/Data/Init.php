<?php

namespace Wyomind\SimpleGoogleShopping\Setup\Patch\Data;

use Magento\Framework\Module\ModuleResource;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\Website;
use Wyomind\SimpleGoogleShopping\Model\ResourceModel\Store\CollectionFactory;

class Init implements DataPatchInterface, PatchRevertableInterface
{
    protected $coreDate;
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;


    const version = "1.0.0";
    private $dataVersion = 0;

    private $storeId = 0;


    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param CollectionFactory $orderCollectionFactory
     * @param Website $websiteModel
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        ModuleResource           $moduleResource,
        DateTime                 $coreDate,
        CollectionFactory        $storeCollectionFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->storeId = $storeCollectionFactory->create()->getFirstStoreId();
        $this->coreDate = $coreDate;
        $this->dataVersion = $moduleResource->getDataVersion("Wyomind_SimpleGoogleShopping");
    }

    /**
     * @inheritdoc
     */
    public function apply()
    {

        if (version_compare($this->dataVersion, self::version) >= 0) {
            $this->moduleDataSetup->getConnection()->startSetup();
            $installer = $this->moduleDataSetup;

            $data = ['templates' => [], 'functions' => []];
            $data['templates'][] = [
                'simplegoogleshopping_id' => null,
                'simplegoogleshopping_name' => 'Google Shopping',
                'simplegoogleshopping_filename' => 'GoogleShopping_full.xml',
                'simplegoogleshopping_path' => '/feeds/',
                'simplegoogleshopping_time' => $this->coreDate->date('Y-m-d H:i:s'),
                'store_id' => $this->storeId,
                'simplegoogleshopping_url' => 'http://www.example.com',
                'simplegoogleshopping_title' => 'Full data feed',
                'simplegoogleshopping_description' => 'This is the main feed for your online product data for Google Shopping and should be submitted at least every 30 days.',
                'simplegoogleshopping_xmlitempattern' => '<!-- Basic Product Information -->
<g:id>{{product.sku}}</g:id>
<title>{{product.name}}</title>
<link>{{parent.url | product.url}}</link>
<description>{{product.description output="strip_tags($self)"}}</description>
<g:google_product_category>{{product.google_product_category | parent.google_product_category}}</g:google_product_category>

<g:product_type>{{parent.categories nth=0 | product.categories nth=0 }}</g:product_type>
<g:product_type>{{parent.categories nth=1 | product.categories nth=1 }}</g:product_type>
<g:product_type>{{parent.categories nth=2 | product.categories nth=2 }}</g:product_type>
<g:product_type>{{parent.categories nth=3 | product.categories nth=3 }}</g:product_type>
<g:product_type>{{parent.categories nth=4 | product.categories nth=4 }}</g:product_type>
<g:product_type>{{parent.categories nth=5 | product.categories nth=5 }}</g:product_type>
<g:product_type>{{parent.categories nth=6 | product.categories nth=6 }}</g:product_type>
<g:product_type>{{parent.categories nth=7 | product.categories nth=7 }}</g:product_type>
<g:product_type>{{parent.categories nth=8 | product.categories nth=8 }}</g:product_type>
<g:product_type>{{parent.categories nth=9 | product.categories nth=9 }}</g:product_type>

<g:image_link>{{parent.image_link index="0"| product.image_link index="0"}}</g:image_link>
<g:additional_image_link>{{parent.image_link index="1"| product.image_link index="1"}}</g:additional_image_link>
<g:additional_image_link>{{parent.image_link index="2"| product.image_link index="2"}}</g:additional_image_link>
<g:additional_image_link>{{parent.image_link index="3"| product.image_link index="3"}}</g:additional_image_link>
<g:additional_image_link>{{parent.image_link index="4"| product.image_link index="4"}}</g:additional_image_link>
<g:additional_image_link>{{parent.image_link index="5"| product.image_link index="5"}}</g:additional_image_link>

<!-- Availability & Price -->
<g:availability>{{product.availability}}</g:availability>

<g:price>{{product.price currency=USD vat_rate=0 suffix=" USD"}}</g:price>
<g:sale_price>{{product.sale_price currency=USD vat_rate=0 suffix=" USD"}}</g:sale_price>
<g:sale_price_effective_date>{{product.sale_price_effective_date}}</g:sale_price_effective_date>

<g:condition>{{product.condition}}</g:condition>
<!-- Unique Product Identifiers-->
<g:brand>{{product.brand}}</g:brand>
<g:gtin>{{product.upc}}</g:gtin>
<g:mpn>{{product.mpn}}</g:mpn>
<g:identifier_exists>TRUE</g:identifier_exists>

<!-- Apparel Products -->
<g:gender>{{product.gender}}</g:gender>
<g:age_group>{{product.age_group}}</g:age_group>
<g:color>{{product.color}}</g:color>
<g:size>{{product.size}}</g:size>


<!-- Product Variants -->
<g:item_group_id>{{parent.sku}}</g:item_group_id>
<g:material>{{product.material}}</g:material>
<g:pattern>{{product.pattern}}</g:pattern>

<!-- Shipping -->
<g:shipping_weight>{{product.weight output="float($self,2)" suffix="kg"}}</g:shipping_weight>

<!-- AdWords attributes -->
<g:custom_label_0>{{product.custom_label_0}}</g:custom_label_0>
<g:custom_label_1>{{product.custom_label_1}}</g:custom_label_1>
<g:custom_label_2>{{product.custom_label_2}}</g:custom_label_2>
<g:custom_label_3>{{product.custom_label_3}}</g:custom_label_3>
<g:custom_label_4>{{product.custom_label_4}}</g:custom_label_4>',
                'simplegoogleshopping_categories' => '*',
                'simplegoogleshopping_category_filter' => '1',
                'simplegoogleshopping_category_type' => '0',
                'simplegoogleshopping_type_ids' => 'simple',
                'simplegoogleshopping_visibility' => '1,2,3,4',
                'simplegoogleshopping_attribute_sets' => '*',
                'simplegoogleshopping_attributes' => '[]',
                'cron_expr' => '{"days":["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"],"hours":["04:00"]}'
            ];

            $data['templates'][] = [
                'simplegoogleshopping_id' => null,
                'simplegoogleshopping_name' => 'Google Shopping Inventory',
                'simplegoogleshopping_filename' => 'GoogleShopping_inventory.xml',
                'simplegoogleshopping_path' => '/feeds/',
                'simplegoogleshopping_time' => $this->coreDate->date('Y-m-d H:i:s'),
                'store_id' => $this->storeId,
                'simplegoogleshopping_url' => 'http://www.example.com',
                'simplegoogleshopping_title' => 'Inventory data feed',
                'simplegoogleshopping_description' => 'Submit this feed throughout the day to update your price, availability and/or sale price information for specific items already submitted in your full product feed.',
                'simplegoogleshopping_xmlitempattern' => '<g:id>{{product.sku}}</g:id>
<g:availability>{{product.availability}}</g:availability>
<g:price>{{product.price currency=USD vat_rate=0}}</g:price>
<g:sale_price>{{product.sale_price currency=USD vat_rate=0}}</g:sale_price>',
                'simplegoogleshopping_categories' => '*',
                'simplegoogleshopping_category_filter' => '1',
                'simplegoogleshopping_category_type' => '0',
                'simplegoogleshopping_type_ids' => 'simple',
                'simplegoogleshopping_visibility' => '1,2,3,4',
                'simplegoogleshopping_attribute_sets' => '*',
                'simplegoogleshopping_attributes' => '[]',
                'cron_expr' => '{"days":["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"],"hours":["05:00"]}'
            ];

            $data['functions'] = [
                [
                    'id' => null,
                    'script' => '<?php function float($self,$dec) { return number_format((float)$self,$dec,".",""); } ?>'
                ],
                [
                    'id' => null,
                    'script' => "<?php function cleaner(\$self) {\$value_cleaned = preg_replace('/' . '[\\x00-\\x1F\\x7F]' . '|[\\x00-\\x7F][\\x80-\\xBF]+' . '|([\\xC0\\xC1]|[\\xF0-\\xFF])[\\x80-\\xBF]*' . '|[\\xC2-\\xDF]((?![\\x80-\\xBF])|[\\x80-\\xBF]{2,})' . '|[\\xE0-\\xEF](([\\x80-\\xBF](?![\\x80-\\xBF]))|' . '(?![\\x80-\\xBF]{2})|[\\x80-\\xBF]{3,})' . '/S', ' ', \$self); \$value = str_replace('&#153;', '', \$value_cleaned); return \$value; } ?>"
                ],
                [
                    'id' => null,
                    'script' => "<?php function sgs_strtoupper(\$self) {\nreturn mb_strtoupper(\$self, \"UTF8\");\n}\n?>"
                ],
                [
                    'id' => null,
                    'script' => "<?php function sgs_strtolower(\$self) {\nreturn mb_strtolower(\$self, \"UTF8\");\n}\n?>"
                ],
                [
                    'id' => null,
                    'script' => "<?php function sgs_implode(\$sep,\$self) {\nreturn (is_array(\$self)) ? implode(\$sep, \$value) : \$self;\n}\n?>"
                ],
                [
                    'id' => null,
                    'script' => '<?php function sgs_html_entity_decode($self) { return html_entity_decode($self, ENT_QUOTES, "UTF-8"); } ?>'
                ],
                [
                    'id' => null,
                    'script' => "<?php function sgs_strip_tags(\$self) {\nreturn strip_tags(preg_replace(['!<br />!isU','!<br/>!isU','!<br>!isU'], [\" \",\" \",\" \"], \$self));\n} ?>"
                ],
                [
                    'id' => null,
                    'script' => "<?php function sgs_htmlentities(\$self) {\nreturn htmlspecialchars(\$self);\n}\n?>"
                ],
                [
                    'id' => null,
                    'script' => "<?php function sgs_substr(\$self,\$len,\$end) {\n\$value = substr(\$self, 0,\$len - 3);\n\$s = strrpos(\$value, \" \");\n\$value = substr(\$value, 0, \$s) . \$end;\nreturn \$value;\n} \n?>"
                ],
                [
                    'id' => null,
                    'script' => "<?php function inline(\$self) {\nreturn preg_replace('/(\r\n|\n|\r|\r\n|\t)/s', '', \n\$self);\n}\n?>"
                ]
            ];

            foreach ($data['templates'] as $template) {
                $installer->getConnection()->insert($installer->getTable('simplegoogleshopping_feeds'), $template);
            }

            foreach ($data['functions'] as $function) {
                $installer->getConnection()->insert($installer->getTable('simplegoogleshopping_functions'), $function);
            }

            $this->moduleDataSetup->getConnection()->endSetup();
        }
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [
            \Wyomind\SimpleGoogleShopping\Setup\Patch\Schema\Init::class
        ];
    }

    public function revert()
    {
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }
}
