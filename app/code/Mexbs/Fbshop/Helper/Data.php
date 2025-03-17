<?php
namespace Mexbs\Fbshop\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Catalog\Model\Product\Attribute\Source\Status as ProductStatus;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    const FEED_FILE_NAME = "fb_feed";
    const FEED_FILE_LOCATION_STATIC = "static";
    const FEED_FILE_LOCATION_ROOT = "root";
    const FEED_FILE_EXTENSION = "csv";
    const STATUS_SUCCESS = 'success';
    const STATUS_ERROR = 'error';
    const FEED_GENERATION_IN_PROGRESS_FLAG_CODE = 'feed_generation_in_progress';
    const FEED_GENERATION_ALT_FLAG = 'fb_feed_alt';
    const CRON_JOB_CODE = "fbshop_generate_feed";
    const CRON_JOB_CODE_TRIGGERED_MANUALLY = "fbshop_generate_feed_now";
    const FEED_ID_XML_PATH = "fbshop/feed/feed_id";
    const PROGRESS_FILE_NAME = 'fbfp.txt';
    const CURRENT_FEED_LOG = 'current_feed_log';
    const FB_COOKIE_NAME = 'is_from_fb';
    const FROMFB_PARAM_NAME = 'is_from_fb';

    public static $variationFields = ['color', 'pattern', 'material', 'size'];

    protected $mappingFactory;
    protected $mappingCollectionFactory;
    protected $customOptionsMappingCollectionFactory;
    protected $curlClient;
    protected $productCollectionFactory;
    protected $configurableType;
    protected $productFactory;
    protected $productResource;
    protected $stockConfiguration;
    protected $stockRegistryProvider;
    protected $storeManager;
    protected $csvParser;
    protected $filesystem;
    protected $categoryFactory;
    protected $date;
    protected $eavAttribute;
    protected $authSession;
    protected $feedLogFactory;
    protected $file;
    protected $scheduleFactory;
    protected $scheduleCollectionFactory;
    protected $progressFileHandler;
    protected $configWriter;
    protected $cache;
    protected $scheduleStoreFactory;
    protected $config;
    protected $fbProductUrl;
    protected $imageFactory;
    protected $catalogProductVisibility;
    protected $customOptionsMappingsTitlesToFields = null;
    protected $customOptionsCollectionFactory;
    protected $customOptionRepository;
    protected $currencyFactory;
    protected $currency;
    protected $currentCurrencySymbol;
    protected $localeDate;
    protected $resourceRuleFactory;
    protected $resource;
    protected $eavSetup;
    protected $connection;
    protected $taxCalculation;
    protected $moduleManager;
    protected $cookieManager;
    protected $cookieMetadataFactory;
    protected $priceCurrency;
    protected $isProductSalable;
    protected $getProductSalableQty;
    protected $stockResolver;
    protected $objectManager;
	private $flagManager;

    public static $fields = [
        'additional_image_link',
        'additional_variant_attribute',
        'age_group',
        'android_app_name',
        'android_package',
        'android_url',
        'availability',
        'availability_date',
        'brand',
        'color',
        'condition',
        'custom_label_0',
        'custom_label_1',
        'custom_label_2',
        'custom_label_3',
        'description',
        'expiration_date',
        'fb_product_category',
        'gender',
        'google_product_category',
        'gtin',
        'id',
        'image_link',
        'inventory',
        'ios_app_name',
        'ios_app_store_id',
        'ios_url',
        'item_group_id',
        'link',
        'material',
        'mpn',
        'offer_price',
        'offer_price_effective_date',
        'pattern',
        'price',
        'product_type',
        'return_policy_info',
        'rich_text_description',
        'sale_price',
        'sale_price_effective_date',
        'shipping',
        'size',
        'title',
        'windows_phone_app_id',
        'windows_phone_app_name',
        'windows_phone_url'
    ];


    public static $variationsAttributeFields = [
        'color',
        'size',
        'material',
        'pattern'
    ];

    public static $requiredFields = [
        'id',
        'availability',
        'description',
        'image_link',
        'link',
        'price',
        'title'
    ];

    public static $requiredOneOfFields = [
        [
            'gtin',
            'mpn',
            'brand'
        ]
    ];

    public static $defaultCompletedBySystemFields = [
        'id',
        'availability',
        'image_link',
        'link',
        'price',
        'sale_price',
        'sale_price_effective_date'
    ];


    public function __construct(
        \Mexbs\Fbshop\Model\ResourceModel\AttributesMapping\CollectionFactory $mappingCollectionFactory,
        \Mexbs\Fbshop\Model\ResourceModel\CustomOptionsMapping\CollectionFactory $customOptionsMappingCollectionFactory,
        \Mexbs\Fbshop\Framework\FlagManager $flagManager,
        \Mexbs\Fbshop\HTTP\Client\Curl $curlClient,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurableType,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\ResourceModel\Product $productResource,
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration,
        \Magento\CatalogInventory\Model\Spi\StockRegistryProviderInterface $stockRegistryProvider,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\File\Csv $csvParser,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute $eavAttribute,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Mexbs\Fbshop\Model\LogFactory $feedLogFactory,
        \Magento\Framework\Filesystem\Io\File $file,
        \Magento\Cron\Model\ScheduleFactory $scheduleFactory,
        \Magento\Cron\Model\ResourceModel\Schedule\CollectionFactory $scheduleCollectionFactory,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
        \Magento\Framework\App\Config\ReinitableConfigInterface $config,
        \Magento\Framework\App\Cache\Type\Config $cache,
        \Mexbs\Fbshop\Model\ScheduleStoreFactory $scheduleStoreFactory,
        \Mexbs\Fbshop\Model\Product\Url $fbProductUrl,
        \Magento\Framework\Image\Factory $imageFactory,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Catalog\Model\ResourceModel\Product\Option\CollectionFactory $customOptionsCollectionFactory,
        \Magento\Catalog\Api\ProductCustomOptionRepositoryInterface $customOptionRepository,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Directory\Model\Currency $currency,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\CatalogRule\Model\ResourceModel\RuleFactory $resourceRuleFactory,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Eav\Setup\EavSetup $eavSetup,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Tax\Api\TaxCalculationInterface $taxCalculation,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->mappingCollectionFactory = $mappingCollectionFactory;
        $this->customOptionsMappingCollectionFactory = $customOptionsMappingCollectionFactory;
        $this->flagManager = $flagManager;
        $this->curlClient = $curlClient;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->configurableType = $configurableType;
        $this->productFactory = $productFactory;
        $this->productResource = $productResource;
        $this->stockConfiguration = $stockConfiguration;
        $this->stockRegistryProvider = $stockRegistryProvider;
        $this->storeManager = $storeManager;
        $this->csvParser = $csvParser;
        $this->filesystem = $filesystem;
        $this->categoryFactory = $categoryFactory;
        $this->date = $date;
        $this->eavAttribute = $eavAttribute;
        $this->authSession = $authSession;
        $this->feedLogFactory = $feedLogFactory;
        $this->file = $file;
        $this->scheduleFactory = $scheduleFactory;
        $this->scheduleCollectionFactory = $scheduleCollectionFactory;
        $this->configWriter = $configWriter;
        $this->config = $config;
        $this->cache = $cache;
        $this->scheduleStoreFactory = $scheduleStoreFactory;
        $this->imageFactory = $imageFactory;
        $this->fbProductUrl = $fbProductUrl;
        $this->customOptionsCollectionFactory = $customOptionsCollectionFactory;
        $this->customOptionRepository = $customOptionRepository;
        $this->currencyFactory = $currencyFactory;
        $this->currency = $currency;
        $this->catalogProductVisibility = $catalogProductVisibility;
        $this->localeDate = $localeDate;
        $this->resourceRuleFactory = $resourceRuleFactory;
        $this->resource = $resource;
        $this->eavSetup = $eavSetup;
        $this->taxCalculation = $taxCalculation;
        $this->moduleManager = $moduleManager;
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->priceCurrency = $priceCurrency;
        $this->objectManager = $objectManager;
        $this->isProductSalable = null;
        $this->getProductSalableQty = null;
        $this->stockResolver = null;
        if($this->moduleManager->isEnabled("Magento_Inventory")) {
            $this->isProductSalable = $this->objectManager->create('Magento\InventorySalesApi\Api\IsProductSalableInterface');
            $this->getProductSalableQty = $this->objectManager->create('Magento\InventorySalesApi\Api\GetProductSalableQtyInterface');
            $this->stockResolver = $this->objectManager->create('Magento\InventorySalesApi\Api\StockResolverInterface');
        }
        $this->connection = $resource->getConnection();
        parent::__construct($context);
    }

    public function getCurrentCurrencySymbol($store){
        if(!$this->currentCurrencySymbol){
            $currencyCode = $store->getCurrentCurrencyCode();
            $currency = $this->currencyFactory->create()->load($currencyCode);
            $this->currentCurrencySymbol = $currency->getCurrencySymbol();
        }
        return $this->currentCurrencySymbol;
    }

    public function scheduleFeedGeneration($storeId = null)
    {
        if(!is_numeric($storeId)){
            throw new \Exception("Invalid store ID");
        }
        $store = $this->storeManager->getStore($storeId);
        if(!$store || !$store->getName()){
            throw new \Exception("The store doesn't exist");
        }

        $schedule = $this->scheduleFactory->create()
            ->setJobCode(self::CRON_JOB_CODE_TRIGGERED_MANUALLY)
            ->setStatus(\Magento\Cron\Model\Schedule::STATUS_PENDING)
            ->setCreatedAt(strftime('%Y-%m-%d %H:%M:%S', $this->date->gmtTimestamp()))
            ->setScheduledAt(strftime('%Y-%m-%d %H:%M', $this->date->gmtTimestamp()))
            ->save();

        $this->scheduleStoreFactory->create()
            ->setScheduleId($schedule->getId())
            ->setStoreId($store->getId())
            ->save();

        return $schedule;
    }

    public function getMissingRequiredFields(){
        $nonDefaultCompleteSystemRequiredFields = array_diff(self::$requiredFields, self::$defaultCompletedBySystemFields);
        $missingRequiredFields = [];

        foreach($nonDefaultCompleteSystemRequiredFields as $field){
            /**
             * @var \Mexbs\Fbshop\Model\ResourceModel\AttributesMapping\Collection $mappingCollection
             */
            $mappingCollection = $this->mappingCollectionFactory->create();
            $mappingCollection->addFieldToFilter('fb_api_field_name', $field);
            if($mappingCollection->getSize() == 0){
                $missingRequiredFields[] = $field;
            }
        }
        return $missingRequiredFields;
    }

    protected function getProgressFileFullPath(){
        return $this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::ROOT)->getAbsolutePath().'var/'.self::PROGRESS_FILE_NAME;
    }

    protected function writeToProgressFile($str){
        $str = sprintf("[%s] %s", $this->date->gmtDate(), $str);
        $this->file->write($this->getProgressFileFullPath(), $str);
    }

    protected function appendToProgressFile($str){
        $str = sprintf("[%s] %s", $this->date->gmtDate(), $str);
        if(!$this->progressFileHandler){
            $this->progressFileHandler = fopen($this->getProgressFileFullPath(), "a+");
        }
        fwrite($this->progressFileHandler, $str);
    }

    protected function isPendingJobExist(){
        /**
         * @var \Magento\Cron\Model\ResourceModel\Schedule\Collection $cronScheduleCollection
         */
        $cronScheduleCollection = $this->scheduleCollectionFactory->create();
        $cronScheduleCollection->addFieldToFilter("job_code", ["in" => [self::CRON_JOB_CODE, self::CRON_JOB_CODE_TRIGGERED_MANUALLY]])
            ->addFieldToFilter("status", \Magento\Cron\Model\Schedule::STATUS_PENDING);
        return ($cronScheduleCollection->getFirstItem() && $cronScheduleCollection->getFirstItem()->getId());
    }

    protected function isRunningJobExist(){
        /**
         * @var \Magento\Cron\Model\ResourceModel\Schedule\Collection $cronScheduleCollection
         */
        $cronScheduleCollection = $this->scheduleCollectionFactory->create();
        $cronScheduleCollection->addFieldToFilter("job_code", ["in" => [self::CRON_JOB_CODE, self::CRON_JOB_CODE_TRIGGERED_MANUALLY]])
            ->addFieldToFilter("status", \Magento\Cron\Model\Schedule::STATUS_RUNNING);
        return ($cronScheduleCollection->getFirstItem() && $cronScheduleCollection->getFirstItem()->getId());
    }

    public function getStatusMessage(){
        $fileStatusMessage = "";
        try{
            $fileStatusMessage = $this->file->read($this->getProgressFileFullPath());
        }catch (\Exception $e){

        }
        if(
            ($fileStatusMessage != "")
            &&
            ($this->isRunningJobExist()
                || (!$this->isRunningJobExist() && !$this->isPendingJobExist()))){
            return $fileStatusMessage;
        }elseif($this->isPendingJobExist()){
            return "The feed generation is scheduled. It should start running soon. You can leave this page and come to check later ...";
        }
        return "";
    }

    public function isGenerationScheduled(){
        return $this->isPendingJobExist();
    }

    public function getFeedStoresDataForFrontend(){
        $storesData = [];
        foreach($this->storeManager->getStores() as $store){
            $storesData[$store->getId()] = [];
            $storesData[$store->getId()]['feed_id'] = $this->getFeedId($store);
            $storesData[$store->getId()]['is_append_feed_id'] = $this->getIsAppendFeedIdToProductId($store);
            $storesData[$store->getId()]['feed_file_url'] = $this->getFeedFileUrl($store->getCode());
            $storesData[$store->getId()]['feed_file_exists'] = $this->getIsFeedFileExists($store->getCode());
        }
        return $storesData;
    }

    /**
     * @param \Magento\Store\Model\Store $store
     */
    public function getFeedId($store){

        return $result = preg_replace("/[^a-zA-Z0-9]+/", "", $store->getConfig(self::FEED_ID_XML_PATH));
    }

    /**
     * @param \Magento\Store\Model\Store $store
     */
    public function getIsFeedGenerationScheduleEnabled($store){
        return $this->getStoreConfig('fbshop/feed/feed_generation_schedule_enabled', $store);
    }

    public function getAddCustomOptions($store){
        return $this->getStoreConfig('fbshop/feed/add_custom_options', $store);
    }

    public function getIsTrackingEnabled($store){
        return $this->getStoreConfig('fbshop/feed/enable_tracking', $store);
    }

    public function getIsAllProductsInFeed($store){
        return $this->getStoreConfig('fbshop/feed/all_products_in_feed', $store);
    }

    public function getFeedFileLocation($store){
        return $this->getStoreConfig('fbshop/feed/feed_file_location', $store);
    }

    /**
     * @param \Magento\Store\Model\Store $store
     */
    public function getIsInheritImageFromParent($store){
        return $this->getStoreConfig('fbshop/feed/inherit_image_from_parent', $store);
    }

    /**
     * @param \Magento\Store\Model\Store $store
     */
    public function getIsIncludeNonvisibleProducts($store){
        return $this->getStoreConfig('fbshop/feed/include_nonvisible_products', $store);
    }

    /**
     * @param \Magento\Store\Model\Store $store
     */
    public function getRemoveOutOfStockProductsFromFeed($store){
        return $this->getStoreConfig('fbshop/feed/remove_out_of_stock_products_from_feed', $store);
    }

    /**
     * @param \Magento\Store\Model\Store $store
     */
    public function getWhereToRedirectFromFacebook($store){
        return $this->getStoreConfig('fbshop/feed/product_redirect', $store);
    }

    /**
     * @param \Magento\Store\Model\Store $store
     */
    public function getDefaultBrandFromConfig($store){
        return $this->getStoreConfig('fbshop/feed/default_brand', $store);
    }

    public function getStoreConfig($path, $store)
    {
        $data = $this->config->getValue($path, ScopeInterface::SCOPE_STORE, $store->getCode());
        if ($data === false) {
            $data = $this->config->getValue($path, ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
        }
        return $data === false ? null : $data;
    }

    public function getMissingFieldsErrorMessage($includeHrefs = false){
        $missingRequiredFields = $this->getMissingRequiredFields();

        if(!$missingRequiredFields){
            return "";
        }

        $missingRequiredFieldsStr = '';
        if(count($missingRequiredFields) > 0){
            $missingRequiredFieldsStr = implode(", ", $missingRequiredFields);
        }
        $isFieldsPlural = (count($missingRequiredFields) > 1);

        if($includeHrefs){
            $errorMessage = sprintf(
                "Please create mapping%s (in Marketing -> Facebook Shop Integration -> Product Attributes Mapping) for the following required field%s: %s. Facebook will reject the products that are missing those fields! (For more information, see the <a href='%s'>%s</a>).",
                ($isFieldsPlural ? "s" : ""),
                ($isFieldsPlural ? "s" : ""),
                $missingRequiredFieldsStr.($missingRequiredFieldsStr != "" ? ", " : ""),
                "https://developers.facebook.com/docs/marketing-api/dynamic-product-ads/product-catalog#required-fields",
                "Facebook API Documentation"
            );
        }else{
            $errorMessage = sprintf(
                "Please create mapping%s for the following required field%s: %s. (For more information, see the %s at %s).",
                ($isFieldsPlural ? "s" : ""),
                ($isFieldsPlural ? "s" : ""),
                $missingRequiredFieldsStr.($missingRequiredFieldsStr != "" ? ", " : ""),
                "Facebook API Documentation",
                "https://developers.facebook.com/docs/marketing-api/dynamic-product-ads/product-catalog#required-fields"
            );
        }


        return $errorMessage;
    }

    public function getFeedFileUrl($storeCode){
        return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB)."fbshop/download/feed?store_code=".$storeCode;
    }


    protected function getProductImageUrl($product, $store, $parentProduct=null){
        $imageName = $product->getImage();
        if((!preg_match("/[\.]+/",$imageName) || $this->getIsInheritImageFromParent($store)) && $parentProduct){
            $imageName = $parentProduct->getImage();
        }
        if($product->getIsResizeMainImageForFb()){
            $fbImagePath = $this->getFbImagePath().$imageName;
            if($this->file->fileExists($fbImagePath)){
                return $this->getFbImageUrl().$imageName;
            }
        }
        return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' . $imageName;
    }

    protected function getNonFbProductImageUrl($product, $parentProduct=null){
        $imageName = $product->getImage();
        if(!preg_match("/[\.]+/",$imageName) && $parentProduct){
            $imageName = $parentProduct->getImage();
        }
        return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' . $imageName;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    protected function getAdditionalProductImageUrls($product, $parentProduct=null){
        $imageUrls = [];
        $productGalleryImages = $product->getMediaGalleryImages();

        if(count($productGalleryImages)){
            foreach($product->getMediaGalleryImages() as $image){
                $imageUrls[] = $image['url'];
            }
        }elseif($parentProduct){
            $parentProductGalleryImages = $parentProduct->getMediaGalleryImages();

            if(count($parentProductGalleryImages)){
                foreach($parentProduct->getMediaGalleryImages() as $image){
                    $imageUrls[] = $image['url'];
                }
            }
        }

        return $imageUrls;
    }

    public function getDisplayPricesInclTax($store){
        return $this->getStoreConfig('fbshop/feed/is_price_incl_tax', $store);
    }

    public function getCustomerGroupIdForTax($store){
        $configValue = $this->getStoreConfig('fbshop/feed/customer_group_id_for_tax', $store);
        return ($configValue === null ? 0 : $configValue);
    }

    protected function getPriceInStoreCurrency($amount, $store){
        return $this->priceCurrency->convertAndRound($amount, $store);
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return mixed
     */
    protected function getProductPrice($product, $store){
        $priceExclTax = 0;

        if(($product->getTypeId() == \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE
                && $product->getPriceType() == \Magento\Bundle\Model\Product\Price::PRICE_TYPE_DYNAMIC)
            || ($product->getTypeId() == \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE)){



            $priceModel = $product->getPriceInfo()->getPrice(\Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE);
            $priceExclTaxInMainCurrency = strval($priceModel->getMinimalPrice());
            $priceExclTax = $this->getPriceInStoreCurrency($priceExclTaxInMainCurrency, $store);

            if(($priceExclTax == 0)
                && ($product->getTypeId() == \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE)){
                throw new \Exception(sprintf(
                    "It seems that the children of the bundled product (ID: %s) are not assigned to the store (%s). Please assign them and try again.",
                    $product->getId(),
                    $store->getCode()
                ));
            }
        }else{
            $priceModel = $product->getPriceInfo()->getPrice(\Magento\Catalog\Pricing\Price\RegularPrice::PRICE_CODE);
            $priceExclTaxInMainCurrency = $priceModel->getValue();
            $priceExclTax = $this->getPriceInStoreCurrency($priceExclTaxInMainCurrency, $store);
        }

        $price = $priceExclTax;
        if($this->getDisplayPricesInclTax($store)){
            $price = $this->getPriceWithTax($product, $priceExclTax, $store);
        }

        return $price;
    }

    public function getPriceWithTax($product, $priceExclTax, $store){
        $taxRate = 0;
        $taxClassId = $product->getTaxClassId();
        if ((int) $this->getStoreConfig('tax/calculation/price_includes_tax', $store) !== 1) {
            //catalog prices are excluding tax
            $taxRate = $this->taxCalculation->getCalculatedRate($taxClassId);
        }
        return round($priceExclTax + ($priceExclTax * ($taxRate / 100)), 2);
    }

    protected function getProductSpecialPrice($product, $store){
        $priceModel = $product->getPriceInfo()->getPrice(\Magento\Catalog\Pricing\Price\SpecialPrice::PRICE_CODE);
        $priceExclTax = $priceModel->getValue();

        $price = $priceExclTax;
        if($this->getDisplayPricesInclTax($store)){
            $price = $this->getPriceWithTax($product, $priceExclTax, $store);
        }

        return $price;
    }

    protected function getProductCatalogRulesPrice($product, $store, $generalCustomerGroupId){
        $date = $this->localeDate->scopeDate($store->getId());
        $websiteId = $store->getWebsiteId();

        $priceExclTax = $this->resourceRuleFactory->create()->getRulePrice($date, $websiteId, $generalCustomerGroupId, $product->getId());
        $price = $priceExclTax;
        if($this->getDisplayPricesInclTax($store)){
            $price = $this->getPriceWithTax($product, $priceExclTax, $store);
        }

        return $price;
    }

    protected function getStockStatus($product, $store){
        if(!$this->moduleManager->isEnabled("Magento_Inventory")){
            $websiteId = $store->getWebsiteId();
            $stockItem = $this->stockRegistryProvider->getStockItem($product->getId(), $websiteId);
            $status = $stockItem->getIsInStock();
            if($status === null){
                $stockItem = $this->stockRegistryProvider->getStockItem($product->getId(), $this->stockConfiguration->getDefaultScopeId());
                $status = $stockItem->getIsInStock();
            }
        }else{
            $stockId = $this->getStockIdForCurrentWebsite($store);
            $status = (int)$this->isProductSalable->execute($product->getSku(), $stockId);
        }

        return $status;
    }

    protected function getStockIdForCurrentWebsite($store){
        $websiteCode = $store->getWebsite()->getCode();

        $stock = $this->stockResolver->execute('website', $websiteCode);
        return (int)$stock->getStockId();
    }

    protected function getStockQty($product, $store){
        if(!$this->moduleManager->isEnabled("Magento_Inventory")) {
            $websiteId = $store->getWebsiteId();
            $stockItem = $this->stockRegistryProvider->getStockItem($product->getId(), $websiteId);
            $qty = $stockItem->getQty();
            if ($qty === null) {
                $stockItem = $this->stockRegistryProvider->getStockItem($product->getId(), $this->stockConfiguration->getDefaultScopeId());
                $qty = $stockItem->getQty();
            }
        }else{
            $stockId = $this->getStockIdForCurrentWebsite($store);
            $qty = (int)$this->getProductSalableQty->execute($product->getSku(), $stockId);
        }
        return $qty;
    }

    /**
     * @param \Magento\Store\Model\Store $store
     */
    public function getIsApplyCatalogRules($store){
        return $this->getStoreConfig('fbshop/feed/is_apply_catalog_rules', $store);
    }

    /**
     * @param \Magento\Store\Model\Store $store
     */
    public function getCustomerGroupIdForCatalogRules($store){
        return $this->getStoreConfig('fbshop/feed/customer_group_id_for_catalog_rules', $store);
    }

    /**
     * @param \Magento\Store\Model\Store $store
     */
    public function getFieldToConstructId($store){
        return $this->getStoreConfig('fbshop/feed/field_to_construct_id', $store);
    }

    /**
     * @param \Magento\Store\Model\Store $store
     */
    public function getIsAppendFeedIdToProductId($store){
        return $this->getStoreConfig('fbshop/feed/append_feed_id_to_product_ids', $store);
    }

    /**
     * @param \Magento\Store\Model\Store $store
     */
    public function getIsAppendParentIdToProductId($store){
        return $this->getStoreConfig('fbshop/feed/append_parent_id_to_product_ids', $store);
    }

    public function constructProductId($product, $fbFeedId, $store, $parentProduct=null){
        $constructedProductId = "";
        if($this->getIsAppendFeedIdToProductId($store)){
            $constructedProductId .= $fbFeedId."_";
        }
        if($this->getIsAppendParentIdToProductId($store) && $parentProduct){
            if($this->getFieldToConstructId($store) == "sku"){
                $parentIdToAppend = ($parentProduct->getSku() ? $parentProduct->getSku()."_" : "");
            }else{
                $parentIdToAppend = ($parentProduct->getId() ? $parentProduct->getId()."_" : "");
            }
            $constructedProductId .= $parentIdToAppend;
        }
        if($this->getFieldToConstructId($store) == "sku"){
            $constructedProductId .= $product->getSku();
        }else{
            $constructedProductId .= $product->getId();
        }
        return $constructedProductId;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param $feedHeader
     * @param $fbFeedId
     * @return array
     */
    protected function getProductFeedRow($product, $feedHeader, $fbFeedId, $store, $parentProduct = null){
        $currencyCode = $store->getCurrentCurrencyCode();

        $feedHeaderToIndexes = array_flip($feedHeader);

        $productSpecialPrice = $this->getProductSpecialPrice($product, $store);

        $productCatalogRulesPrice = $productSpecialPrice;

        if($this->getIsApplyCatalogRules($store)){
            $productCatalogRulesPrice = $this->getProductCatalogRulesPrice($product, $store, $this->getCustomerGroupIdForCatalogRules($store));
        }

        $productSalePrice = 0;
        if($productSpecialPrice > 0 && $productCatalogRulesPrice > 0){
            $productSalePrice = min($productSpecialPrice, $productCatalogRulesPrice);
        }elseif($productSpecialPrice > 0){
            $productSalePrice = $productSpecialPrice;
        }elseif($productCatalogRulesPrice > 0){
            $productSalePrice = $productCatalogRulesPrice;
        }

        $specialPriceFromDate = $this->date->gmtDate("c", $product->getSpecialFromDate());
        if($product->getSpecialToDate()){
            $specialPriceToDate = $this->date->gmtDate("c", str_replace("00:00:00", "23:59:59", $product->getSpecialToDate()));
        }else{
            $specialPriceToDate = $this->date->gmtDate("c", time()+60*60*24*365*10);
        }

        $stockStatus = $this->getStockStatus($product, $store);
        $stockQty = $this->getStockQty($product, $store);

        $productFeedRow = [
            $feedHeaderToIndexes['id'] => $this->constructProductId($product, $fbFeedId, $store, ($parentProduct && $parentProduct->getId() ? $parentProduct : null)),
            $feedHeaderToIndexes['availability'] => (($stockStatus == \Magento\CatalogInventory\Model\Stock::STOCK_IN_STOCK) ?  "in stock" : "out of stock"),
            $feedHeaderToIndexes['image_link'] => $this->getProductImageUrl($product, $store, $parentProduct),
            $feedHeaderToIndexes['price'] => $this->getProductPrice($product, $store)." ".$currencyCode,
            $feedHeaderToIndexes['sale_price'] => ($productSalePrice > 0 ? $productSalePrice." ".$currencyCode : ""),
            $feedHeaderToIndexes['sale_price_effective_date'] => ($productSalePrice > 0 ? $specialPriceFromDate."/".$specialPriceToDate : ""),
            $feedHeaderToIndexes['link'] => $this->getProductUrl($product, [], $store),
            $feedHeaderToIndexes['inventory'] => ((($stockQty == 0) && ($stockStatus == \Magento\CatalogInventory\Model\Stock::STOCK_IN_STOCK)) ? '' : $stockQty),
        ];

        $productFeedRow[$feedHeaderToIndexes['product_type']] = implode("|",array_map(
            function($categoryId){
                /**
                 * @var \Magento\Catalog\Model\Category $category
                 */
                $category = $this->categoryFactory->create()->load($categoryId);
                return $category->getName();
            },
            $product->getCategoryIds()
        ));


        $nonFbImageUrl = $this->getNonFbProductImageUrl($product, $parentProduct);
        $additionalProductImageUrls = array_diff($this->getAdditionalProductImageUrls($product, $parentProduct), [$nonFbImageUrl]);
        if(count($additionalProductImageUrls) > 0){
            $productFeedRow[$feedHeaderToIndexes['additional_image_link']] = implode(",",$additionalProductImageUrls);
        }

        /**
         * @var \Mexbs\Fbshop\Model\ResourceModel\AttributesMapping\Collection $mappingCollection
         */
        $mappingCollection = $this->mappingCollectionFactory->create();
        foreach($mappingCollection as $mapping){
            $attributeValue = $this->productResource->getAttribute($mapping->getAttributeCode())->getFrontend()->getValue($product);
            if($mapping->getFbApiFieldName() == "rich_text_description"){
                if(trim($attributeValue) == "" && isset($parentProduct)){
                    $attributeValue = $this->productResource->getAttribute($mapping->getAttributeCode())->getFrontend()->getValue($parentProduct);
                }
            }elseif($mapping->getFbApiFieldName() == "description"){
                if(trim($attributeValue) == "" && isset($parentProduct)){
                    $attributeValue = $this->productResource->getAttribute($mapping->getAttributeCode())->getFrontend()->getValue($parentProduct);
                }
                $attributeValue = strip_tags($attributeValue);
                $attributeValue = trim($attributeValue);
                $attributeValue = substr($attributeValue, 0, 9990);
                if(!preg_match("/[a-z]/", $attributeValue)){
                    $attributeValue = strtolower($attributeValue);
                }
            }elseif($mapping->getFbApiFieldName() == "title"){
                $attributeValue = substr($attributeValue, 0, 149);
                if(!preg_match("/[a-z]/", $attributeValue)){
                    $attributeValue = strtolower($attributeValue);
                }
            }
            $productFeedRow[$feedHeaderToIndexes[$mapping->getFbApiFieldName()]] = $attributeValue;
        }

        if(!isset($productFeedRow[$feedHeaderToIndexes['condition']]) || $productFeedRow[$feedHeaderToIndexes['condition']] == ""){
            $productFeedRow[$feedHeaderToIndexes['condition']] = "new";
        }
        if(!isset($productFeedRow[$feedHeaderToIndexes['brand']]) || $productFeedRow[$feedHeaderToIndexes['brand']] == ""){
            $productFeedRow[$feedHeaderToIndexes['brand']] = $this->getDefaultBrandFromConfig($store);;
        }

        return $productFeedRow;
    }

    public function getCustomOptionsVariationRows($product, $productFeedRow, $feedHeader, $store, $addCustomOptionRows = true){
        $maxCustomOptionNumberToInclude = 4; //This is a safety measure - since large number of custom options will mae the feed file grow exponentially
        $feedHeaderToIndexes = array_flip($feedHeader);
        $currencySymbol = $this->getCurrentCurrencySymbol($store);

        $customOptionsMappingsTitlesToFields = $this->getCustomOptionsMappingsTitlesToFields();
        $allCustomOptionsForProduct = $this->getAllCustomOptionsForProduct($product);

        if(!$addCustomOptionRows && count($allCustomOptionsForProduct)){
            $productFeedRow[$feedHeaderToIndexes['link']] = $this->fbProductUrl->getProductUrlForStore($product, $store);

            return [$productFeedRow];
        }

        $validUsedAttributesFbFields = [];

        $usedHardcodedVariations = [];
        $usedTitles = [];

        $currentCustomOptionIndex = 0;
        foreach($allCustomOptionsForProduct as $mappedCustomOptionForProduct){
            if($currentCustomOptionIndex >= $maxCustomOptionNumberToInclude){
                break;
            }
            /**
             * @var \Magento\Catalog\Api\Data\ProductCustomOptionInterface $customOption
             */
            $customOption = $this->customOptionRepository->get($product->getSku(), $mappedCustomOptionForProduct->getId());
            if(in_array($customOption->getTitle(), $usedTitles)){
                continue;
            }

            $variationFbField = null;
            if(isset($customOptionsMappingsTitlesToFields[$customOption->getTitle()])){
                $variationFbField = $customOptionsMappingsTitlesToFields[$customOption->getTitle()];

                if(in_array($variationFbField, $usedHardcodedVariations)){
                    continue;
                }
            }

            $customOptionValues = $customOption->getValues();

            $customOptionValuesArray = [];
            /**
             * @var \Magento\Catalog\Api\Data\ProductCustomOptionValuesInterface $customOptionValue
             */
            foreach($customOptionValues as $customOptionValue){
                if($customOptionValue->getPrice() > 0){
                    $priceExclTax = $customOptionValue->getPrice(true);
                    $price = $priceExclTax;
                    if($this->getDisplayPricesInclTax($store)){
                        $price = $this->getPriceWithTax($product, $priceExclTax, $store);
                    }
                    $formattedPrice = $this->currency->format($price, ['symbol' => $currencySymbol], false, false);

                    $customOptionValuesArray[] = [
                        'title' => $customOptionValue->getTitle()." + ".$formattedPrice,
                        'value_id' => $customOptionValue->getOptionTypeId()
                    ];
                }else{
                    $customOptionValuesArray[] = [
                        'title' => $customOptionValue->getTitle(),
                        'value_id' => $customOptionValue->getOptionTypeId()
                    ];
                }
            }

            $validUsedAttributesFbFields[] = [
                'values' => $customOptionValuesArray,
                'title' => $customOption->getTitle(),
                'option_id' => $customOption->getOptionId(),
                'fb_field' => ($variationFbField != null ? $variationFbField : $customOption->getTitle()),
                'is_hardcoded_variation' => ($variationFbField != null)
            ];

            $usedTitles[] = $customOption->getTitle();
            if($variationFbField != null){
                $usedHardcodedVariations[] = $variationFbField;
            }
            $currentCustomOptionIndex++;
        }

        if(count($validUsedAttributesFbFields) > 0){
            $allUsedFbFieldsCombinations = $this->getAllUsedFbFieldsCombinations($validUsedAttributesFbFields);

            $productFeedRows = [];

            $coVariatedProductCount = 1;
            for($currentFieldsCombinationRow=0; $currentFieldsCombinationRow<count($allUsedFbFieldsCombinations); $currentFieldsCombinationRow++){

                $coVariatedProductRowDuplicate = $productFeedRow;
                $customOptionIdsToValueIds = [];
                $additionalVariationsArray = [];

                for($currentFieldIndex=0; $currentFieldIndex<count($allUsedFbFieldsCombinations[0]); $currentFieldIndex++){
                    if($allUsedFbFieldsCombinations[$currentFieldsCombinationRow][$currentFieldIndex]['is_hardcoded_variation']){
                        $coVariatedProductRowDuplicate[$feedHeaderToIndexes[$allUsedFbFieldsCombinations[$currentFieldsCombinationRow][$currentFieldIndex]['fb_field']]]
                            = $allUsedFbFieldsCombinations[$currentFieldsCombinationRow][$currentFieldIndex]['value_title'];
                    }else{
                        $additionalVariationsArray[$allUsedFbFieldsCombinations[$currentFieldsCombinationRow][$currentFieldIndex]['fb_field']]
                            = $allUsedFbFieldsCombinations[$currentFieldsCombinationRow][$currentFieldIndex]['value_title'];
                    }

                    $customOptionIdsToValueIds[$allUsedFbFieldsCombinations[$currentFieldsCombinationRow][$currentFieldIndex]['option_id']] =
                        $allUsedFbFieldsCombinations[$currentFieldsCombinationRow][$currentFieldIndex]['value_id'];
                }
                $coVariatedProductRowDuplicate[$feedHeaderToIndexes['link']] = $this->getProductUrlWithCustomOptions($product, $customOptionIdsToValueIds, $store);
                $coVariatedProductRowDuplicate[$feedHeaderToIndexes['id']] = $productFeedRow[$feedHeaderToIndexes['id']]."_customoption_".$coVariatedProductCount;
                $coVariatedProductRowDuplicate[$feedHeaderToIndexes['item_group_id']] = $productFeedRow[$feedHeaderToIndexes['id']];

                $coVariatedProductRowDuplicate[$feedHeaderToIndexes['additional_variant_attribute']] = '';
                if(count($additionalVariationsArray) > 0){
                    $coVariatedProductRowDuplicate[$feedHeaderToIndexes['additional_variant_attribute']] = $this->buildAdditionalVariationsString($additionalVariationsArray);
                }

                $productFeedRows[] = $coVariatedProductRowDuplicate;
                $coVariatedProductCount++;
            }

            return $productFeedRows;

        }else{
            return [];
        }
    }

    /**
     * Example:
     * For the following variations:
     *
     * Color: Red, Yellow
     * Size: XL, S, M
     * Pattern: Flowers, Stars
     *
     * Resulting combination matrix:
     * Red              XL              Flowers
     * Red              XL              Stars
     * Red              S               Flowers
     * Red              S               Stars
     * Red              M               Flowers
     * Red              M               Stars
     * Yellow           XL              Flowers
     * Yellow           XL              Stars
     * Yellow           S               Flowers
     * Yellow           S               Stars
     * Yellow           M               Flowers
     * Yellow           M               Stars
     *
     * @param $validUsedAttributesFbFields
     * @return array
     */
    protected function getAllUsedFbFieldsCombinations($validUsedAttributesFbFields){
        $combinationsMatrix = [];

        $numberOfRows = 1;
        foreach($validUsedAttributesFbFields as $validUsedAttributesFbField){
            $numberOfRows *= count($validUsedAttributesFbField['values']);
        }

        $modPerField = [];
        $divPerField = [];

        $prevMod = 1;
        foreach($validUsedAttributesFbFields as $validUsedAttributesFbField){
            $modPerField[$validUsedAttributesFbField['title']] = $prevMod*count($validUsedAttributesFbField['values']);
            $divPerField[$validUsedAttributesFbField['title']] = $prevMod;
            $prevMod = $modPerField[$validUsedAttributesFbField['title']];
        }

        for($i=0; $i<$numberOfRows; $i++){
            $fieldIndex = 0;
            foreach($validUsedAttributesFbFields as $validUsedAttributesFbField){
                if(!isset($combinationsMatrix[$i])){
                    $combinationsMatrix[$i] = [];
                }

                $optionValueIndex = intval(floor($i%$modPerField[$validUsedAttributesFbField['title']]/$divPerField[$validUsedAttributesFbField['title']]));

                $combinationsMatrix[$i][$fieldIndex] = [
                    'option_title' => $validUsedAttributesFbField['title'],
                    'option_id' => $validUsedAttributesFbField['option_id'],
                    'value_title' => $validUsedAttributesFbField['values'][$optionValueIndex]['title'],
                    'value_id' => $validUsedAttributesFbField['values'][$optionValueIndex]['value_id'],
                    'fb_field' => $validUsedAttributesFbField['fb_field'],
                    'is_hardcoded_variation' => $validUsedAttributesFbField['is_hardcoded_variation']
                ];

                $fieldIndex++;
            }
        }
        return $combinationsMatrix;
    }

    protected function getAllCustomOptionsForProduct($product){
        return $this->getCustomOptionsForProduct($product);
    }

    protected function getCustomOptionsForProduct($product, $requiredTitles=null){
        /**
         * @var \Magento\Catalog\Model\ResourceModel\Product\Option\Collection $customOptionsCollection
         */
        $customOptionsCollection = $this->customOptionsCollectionFactory->create();
        $customOptionsCollection->addProductToFilter($product)
            ->addTitleToResult(\Magento\Store\Model\Store::DEFAULT_STORE_ID)
            ->addPriceToResult(\Magento\Store\Model\Store::DEFAULT_STORE_ID)
            ->getSelect()->where(
                sprintf("type IN (%s)", $customOptionsCollection->getConnection()->quote([
                    \Magento\Catalog\Model\Product\Option::OPTION_TYPE_DROP_DOWN,
                    \Magento\Catalog\Model\Product\Option::OPTION_TYPE_RADIO
                ]))
            );
        if(is_array($requiredTitles) && count($requiredTitles)>0){
            $customOptionsCollection->getSelect()->where(
                sprintf(
                    "default_option_title.title IN (%s)", $customOptionsCollection->getConnection()->quote($requiredTitles)
                )
            );
        }
        $customOptionsCollection->getSelect()->group('title');

        return $customOptionsCollection;
    }

    protected function getMappedCustomOptionsForProduct($product, $customOptionsMappingsTitles){
        return $this->getCustomOptionsForProduct($product, $customOptionsMappingsTitles);
    }

    protected function getCustomOptionsMappingsTitlesToFields(){
        if(!$this->customOptionsMappingsTitlesToFields){
            $this->customOptionsMappingsTitlesToFields = [];
            /**
             * @var \Mexbs\Fbshop\Model\ResourceModel\CustomOptionsMapping\Collection $customOptionsMappingCollection
             */
            $customOptionsMappingCollection = $this->customOptionsMappingCollectionFactory->create();
            foreach($customOptionsMappingCollection as $customOptionsMapping){
                $this->customOptionsMappingsTitlesToFields[$customOptionsMapping->getCustomOptionTitle()] = $customOptionsMapping->getFbApiFieldName();
            }
        }
        return $this->customOptionsMappingsTitlesToFields;
    }


    protected function addOrderKeysAndCellsByOrder($childProductFeedRow){
        $orderedRow = [];
        for($index=0; $index<count(self::$fields); $index++){
            if(array_key_exists($index, $childProductFeedRow)){
                $orderedRow[$index] = $childProductFeedRow[$index];
            }else{
                $orderedRow[$index] = '';
            }
        }
        return $orderedRow;
    }

    /**
     * @param \Magento\Store\Model\Store $store
     * @return mixed
     */
    public  function getNumberOfProductsToProcess($store){
        try{
            /**
             * @var \Magento\Catalog\Model\ResourceModel\Product\Collection $configurableProductsCollection
             */
            $configurableProductsCollection = $this->productCollectionFactory->create();
            $configurableProductsCollection
                ->addAttributeToFilter("type_id", ['eq' => \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE])
                ->addFieldToFilter('status', ProductStatus::STATUS_ENABLED)
                ->addStoreFilter($store);

            if(!$this->getIsAllProductsInFeed($store)){
                $configurableProductsCollection->addFieldToFilter('is_in_fb_feed', \Magento\Eav\Model\Entity\Attribute\Source\Boolean::VALUE_YES);
            }

            if(!$this->getIsIncludeNonvisibleProducts($store)){
                $configurableProductsCollection->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds());
            }


            $configurableProductIds = [];
            $childrenProductIds = [];
            foreach($configurableProductsCollection as $configurableProduct){
                if($this->getRemoveOutOfStockProductsFromFeed($store)){
                    if($this->getStockStatus($configurableProduct, $store) != \Magento\CatalogInventory\Model\Stock::STOCK_IN_STOCK){
                        continue;
                    }
                }

                $configurableProductId = $configurableProduct->getId();
                $configurableProductIds[] = $configurableProductId;
            }

            $productEntityTypeId = $this->eavSetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);

            $select = $this->connection->select()
                ->from(
                    ['eav_attribute' => $this->resource->getTableName('eav_attribute')], ['attribute_id']
                )->where(sprintf("eav_attribute.attribute_code = 'status' AND entity_type_id = '%s'", $productEntityTypeId));
            $row = $this->connection->fetchRow($select);
            $statusAttributeId = $row['attribute_id'];

            $select = $this->connection->select()
                ->from(
                    ['eav_attribute' => $this->resource->getTableName('eav_attribute')], ['attribute_id']
                )->where(sprintf("eav_attribute.attribute_code = 'is_in_fb_feed' AND entity_type_id = '%s'", $productEntityTypeId));
            $row = $this->connection->fetchRow($select);
            $isInFbFeedAttributeId = $row['attribute_id'];

            if($this->getRemoveOutOfStockProductsFromFeed($store)){
                $select = $this->connection->select()
                    ->from(
                        ['link' => $this->resource->getTableName('catalog_product_super_link')],
                        [
                            'product_id',
                            'status' => 'IFNULL(status_store.value, status_default.value)',
                            'is_in_fb_feed' => 'IFNULL(is_in_fb_feed_store.value, is_in_fb_feed_default.value)',
                            'stock_status' => 'stock_status.stock_status'
                        ]
                    )
                    ->joinLeft(
                        ['stock_status' => $this->resource->getTableName('cataloginventory_stock_status')],
                        sprintf("stock_status.product_id=link.product_id AND stock_status.website_id=0"))
                    ->joinLeft(['status_default' => $this->resource->getTableName('catalog_product_entity_int')],
                        sprintf("status_default.attribute_id=%s AND status_default.store_id=0 AND status_default.entity_id=link.product_id", $statusAttributeId),
                        [])
                    ->joinLeft(['status_store' => $this->resource->getTableName('catalog_product_entity_int')],
                        sprintf("status_store.attribute_id=%s AND status_store.store_id=%s AND status_store.entity_id=link.product_id", $statusAttributeId, $store->getId()),
                        [])
                    ->joinLeft(['is_in_fb_feed_default' => $this->resource->getTableName('catalog_product_entity_int')],
                        sprintf("is_in_fb_feed_default.attribute_id=%s AND is_in_fb_feed_default.store_id=0 AND is_in_fb_feed_default.entity_id=link.product_id", $isInFbFeedAttributeId),
                        [])
                    ->joinLeft(['is_in_fb_feed_store' => $this->resource->getTableName('catalog_category_entity_int')],
                        sprintf("is_in_fb_feed_store.attribute_id=%s AND is_in_fb_feed_store.store_id=%s AND is_in_fb_feed_store.entity_id=link.product_id", $isInFbFeedAttributeId, $store->getId()),
                        []);

                if($this->getIsAllProductsInFeed($store)){
                    $select->where(
                        sprintf(
                            "IFNULL(status_store.value, status_default.value) = 1 AND link.parent_id IN ('%s')",
                            implode("','",$configurableProductIds)
                        ));
                }else{
                    $select->where(
                        sprintf(
                            "IFNULL(status_store.value, status_default.value) = 1 AND IFNULL(is_in_fb_feed_store.value, is_in_fb_feed_default.value)=1 AND link.parent_id IN ('%s')",
                            implode("','",$configurableProductIds)
                        ));
                }
                ;

                foreach($this->connection->fetchAll($select) as $childrenProductData){
                    if($childrenProductData['status'] == 1
                        && ($childrenProductData['is_in_fb_feed'] == 1 || $this->getIsAllProductsInFeed($store))
                        && $childrenProductData['stock_status'] == 1 ){
                        $childrenProductIds[] = $childrenProductData['product_id'];
                    }
                }
            }else{
                $select = $this->connection->select()
                    ->from(
                        ['link' => $this->resource->getTableName('catalog_product_super_link')],
                        [
                            'product_id',
                            'status' => 'IFNULL(status_store.value, status_default.value)',
                            'is_in_fb_feed' => 'IFNULL(is_in_fb_feed_store.value, is_in_fb_feed_default.value)'
                        ]
                    )
                    ->joinLeft(['status_default' => $this->resource->getTableName('catalog_product_entity_int')],
                        sprintf("status_default.attribute_id=%s AND status_default.store_id=0 AND status_default.entity_id=link.product_id", $statusAttributeId),
                        [])
                    ->joinLeft(['status_store' => $this->resource->getTableName('catalog_product_entity_int')],
                        sprintf("status_store.attribute_id=%s AND status_store.store_id=%s AND status_store.entity_id=link.product_id", $statusAttributeId, $store->getId()),
                        [])
                    ->joinLeft(['is_in_fb_feed_default' => $this->resource->getTableName('catalog_product_entity_int')],
                        sprintf("is_in_fb_feed_default.attribute_id=%s AND is_in_fb_feed_default.store_id=0 AND is_in_fb_feed_default.entity_id=link.product_id", $isInFbFeedAttributeId),
                        [])
                    ->joinLeft(['is_in_fb_feed_store' => $this->resource->getTableName('catalog_category_entity_int')],
                        sprintf("is_in_fb_feed_store.attribute_id=%s AND is_in_fb_feed_store.store_id=%s AND is_in_fb_feed_store.entity_id=link.product_id", $isInFbFeedAttributeId, $store->getId()),
                        []);
                ;

                if($this->getIsAllProductsInFeed($store)){
                    $select->where(
                        sprintf(
                            "IFNULL(status_store.value, status_default.value) = 1 AND link.parent_id IN ('%s')",
                            implode("','",$configurableProductIds)
                        ));
                }else{
                    $select->where(
                        sprintf(
                            "IFNULL(status_store.value, status_default.value) = 1 AND IFNULL(is_in_fb_feed_store.value, is_in_fb_feed_default.value)=1 AND link.parent_id IN ('%s')",
                            implode("','",$configurableProductIds)
                        ));
                }

                foreach($this->connection->fetchAll($select) as $childrenProductData){
                    if($childrenProductData['status'] == 1
                        && ($childrenProductData['is_in_fb_feed'] == 1 || $this->getIsAllProductsInFeed($store))){
                        $childrenProductIds[] = $childrenProductData['product_id'];
                    }
                }
            }


            /**
             * @var \Magento\Catalog\Model\ResourceModel\Product\Collection $nonConfigurableProductsCollection
             */
            $nonConfigurableAndNonChildrenProductsCollection = $this->productCollectionFactory->create();
            $nonConfigurableAndNonChildrenProductsCollection
                ->addAttributeToFilter("type_id", ['neq' => \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE])
                ->addFieldToFilter('status', ProductStatus::STATUS_ENABLED)
                ->addStoreFilter($store);

            if(!$this->getIsAllProductsInFeed($store)){
                $nonConfigurableAndNonChildrenProductsCollection->addFieldToFilter('is_in_fb_feed', \Magento\Eav\Model\Entity\Attribute\Source\Boolean::VALUE_YES);
            }

            if(!$this->getIsIncludeNonvisibleProducts($store)){
                $nonConfigurableAndNonChildrenProductsCollection->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds());
            }


            $nonConfigurableAndNonChildrenProductIds = [];

            foreach($nonConfigurableAndNonChildrenProductsCollection as $nonConfigurableAndNonChildrenProduct){
                if($this->getRemoveOutOfStockProductsFromFeed($store)){
                    if($this->getStockStatus($nonConfigurableAndNonChildrenProduct, $store) != \Magento\CatalogInventory\Model\Stock::STOCK_IN_STOCK){
                        continue;
                    }
                }

                $nonConfigurableAndNonChildrenProductId = $nonConfigurableAndNonChildrenProduct->getId();

                if(!in_array($nonConfigurableAndNonChildrenProductId, $configurableProductIds)
                    && !in_array($nonConfigurableAndNonChildrenProductId, $childrenProductIds)){
                    $nonConfigurableAndNonChildrenProductIds[] = $nonConfigurableAndNonChildrenProductId;
                }
            }

            return count($configurableProductIds)+count($childrenProductIds)+count($nonConfigurableAndNonChildrenProductIds);
        }catch(\Exception $e){
            /**
             * @var \Magento\Catalog\Model\ResourceModel\Product\Collection $productsCollection
             */
            $productsCollection = $this->productCollectionFactory->create();
            $productsCollection
                ->addFieldToFilter('status', ProductStatus::STATUS_ENABLED)
                ->addStoreFilter($store);

            if(!$this->getIsAllProductsInFeed($store)){
                $productsCollection->addFieldToFilter('is_in_fb_feed', \Magento\Eav\Model\Entity\Attribute\Source\Boolean::VALUE_YES);
            }
            return $productsCollection->getSize();
        }
    }


    public function getIsGenerationInProgress(){
        $flagLastUpdated = $this->flagManager->getFlagLastUpdate(self::FEED_GENERATION_IN_PROGRESS_FLAG_CODE);
        $flagLastUpdatedTimeStamp = $this->date->timestamp($flagLastUpdated);
        $currentTimestamp = $this->date->timestamp();
        if(($currentTimestamp - $flagLastUpdatedTimeStamp) > 60*30){
            $this->flagManager->deleteFlag(self::FEED_GENERATION_IN_PROGRESS_FLAG_CODE);
        }
        return ($this->flagManager->getFlagData(self::FEED_GENERATION_IN_PROGRESS_FLAG_CODE) == "1");
    }

    public function getLastAlt(){
        return $this->flagManager->getFlagData(self::FEED_GENERATION_ALT_FLAG);
    }

    public function setLastAlt($alt){
        $this->flagManager->saveFlag(self::FEED_GENERATION_ALT_FLAG, $alt);
    }

    protected function setGenerationInProgress(){
        $this->flagManager->saveFlag(self::FEED_GENERATION_IN_PROGRESS_FLAG_CODE, "1");
    }

    protected function unsetGenerationInProgress(){
        $this->flagManager->deleteFlag(self::FEED_GENERATION_IN_PROGRESS_FLAG_CODE);
    }

    protected function buildAdditionalVariationsString($additionalVariationsArray){
        $additionalVariationsString = "";
        $currentIndex = 0;
        foreach($additionalVariationsArray as $variantKey => $variantValue){
            $additionalVariationsString .= str_replace(",", ";", $variantKey.":".$variantValue);
            if($currentIndex < (count($additionalVariationsArray)-1)){
                $additionalVariationsString .= ",";
            }

            $currentIndex++;
        }
        return $additionalVariationsString;
    }

    protected function addParamToLink($link, $paramString){
        if (parse_url($link, PHP_URL_QUERY)){
            return $link."&".$paramString;
        }else{
            return $link."?".$paramString;
        }
    }

    protected function getFeedRows($feedHeader, $fbFeedId, $store){
        $skippedConfigurableCount = 0;
        $feedRows = [];
        $addedProductIds = [];

        $numOfProductsToProcess = $this->getNumberOfProductsToProcess($store);
        $numOfProcessedProducts = 0;
        $currentAlt = ($this->getLastAlt() ? "0" : "1");

        $feedHeaderToIndexes = array_flip($feedHeader);
        /**
         * @var \Magento\Catalog\Model\ResourceModel\Product\Collection $productsCollection
         */
        $productsCollection = $this->productCollectionFactory->create();
        $productsCollection
            ->addFieldToFilter('status', ProductStatus::STATUS_ENABLED)
            ->addAttributeToFilter("type_id", \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE)
            ->addStoreFilter($store)
            ->addPriceData($this->getCustomerGroupIdForTax($store), $store->getWebsite()->getId());

        if(!$this->getIsAllProductsInFeed($store)){
            $productsCollection->addFieldToFilter('is_in_fb_feed', \Magento\Eav\Model\Entity\Attribute\Source\Boolean::VALUE_YES);
        }

        if(!$this->getIsIncludeNonvisibleProducts($store)){
            $productsCollection->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds());
        }

        foreach($productsCollection as $product){
            if(in_array($product->getId(), $addedProductIds)){
                continue;
            }

            /**
             * @var \Magento\Catalog\Model\Product $product
             */
            $product = $this->productFactory->create()->setStoreId($store->getId())->load($product->getId());

            $childrenProducts = $this->configurableType->getUsedProducts($product);
            $usedAttributes = $this->configurableType->getUsedProductAttributes($product);

            $cheapestChildPrice = INF;
            $childrenRows = [];
            $childIndex = 0;

            foreach($childrenProducts as $childProduct){
                /**
                 * @var \Magento\Catalog\Model\Product $childProduct
                 */
                $childProduct = $this->productFactory->create()->setStoreId($store->getId())->load($childProduct->getId());

                if((!$this->getIsAllProductsInFeed($store) && !$childProduct->getIsInFbFeed())
                    || $childProduct->getStatus() != ProductStatus::STATUS_ENABLED){
                    continue;
                }

                if($this->getRemoveOutOfStockProductsFromFeed($store)){
                    if($this->getStockStatus($childProduct, $store) != \Magento\CatalogInventory\Model\Stock::STOCK_IN_STOCK){
                        continue;
                    }
                }

                $childProduct->setName($product->getName());

                $childProductFeedRow = $this->getProductFeedRow($childProduct, $feedHeader, $fbFeedId, $store, $product);

                $childProductFeedRow[$feedHeaderToIndexes['item_group_id']] = $this->constructProductId($product, $fbFeedId, $store);
                $childProductFeedRow[$feedHeaderToIndexes['product_type']] = implode("|",array_map(
                    function($categoryId){
                        $category = $this->categoryFactory->create()->load($categoryId);
                        return $category->getName();
                    },
                    $product->getCategoryIds()
                ));

                $additionalVariationsArray = [];

                $attributeCodesToOptionIds = [];
                foreach($usedAttributes as $usedAttribute){
                    /**
                     * @var \Mexbs\Fbshop\Model\ResourceModel\AttributesMapping\Collection $mappingCollection
                     */
                    $mappingCollection = $this->mappingCollectionFactory->create();
                    $mappingCollection->addFieldToFilter('attribute_code', $usedAttribute->getAttributeCode());

                    $fbFieldName = $usedAttribute->getAttributeCode();
                    if($mappingCollection->getSize() > 0){
                        $mappingFirstItem = $mappingCollection->getFirstItem();
                        $fbFieldName = $mappingFirstItem->getFbApiFieldName();
                    }
                    $attributeCodesToOptionIds[$usedAttribute->getAttributeCode()] = $childProduct->getData($usedAttribute->getAttributeCode());

                    $attributeValue = $this->productResource->getAttribute($usedAttribute->getAttributeCode())->getFrontend()->getValue($childProduct);
                    if(!isset($feedHeaderToIndexes[$fbFieldName])){
                        $usedAttributeLabel = $usedAttribute->getStoreLabel($store->getId());
                        $additionalVariationsArray[$usedAttributeLabel] = $attributeValue;
                    }else{
                        $childProductFeedRow[$feedHeaderToIndexes[$fbFieldName]] = $attributeValue;
                    }
                }

                $childProductFeedRow[$feedHeaderToIndexes['additional_variant_attribute']] = '';
                if(count($additionalVariationsArray) > 0){
                    $childProductFeedRow[$feedHeaderToIndexes['additional_variant_attribute']] = $this->buildAdditionalVariationsString($additionalVariationsArray);
                }

                $childLink = $this->getProductUrl($product, $attributeCodesToOptionIds, $store);
                $childProductFeedRow[$feedHeaderToIndexes['link']] = $childLink;

                $childProductPrice = $this->getProductPrice($childProduct, $store);
                if(is_numeric($childProductPrice) && ($childProductPrice < $cheapestChildPrice)){
                    $cheapestChildPrice = $childProductPrice;
                    $cheapestChildIndex = $childIndex;
                    $cheapestChildLink = $childLink;
                }

                $childrenRows[$childIndex] = $childProductFeedRow;

                $addedProductIds[] = $childProduct->getId();
                $numOfProcessedProducts++;
                $feedProductsNumberStatusMessage = "Processing product ".$numOfProcessedProducts." out of ".$numOfProductsToProcess."\n";
                $this->appendToProgressFile($feedProductsNumberStatusMessage);

                $childIndex++;
            }

            if(isset($cheapestChildIndex) && isset($cheapestChildLink) && $cheapestChildLink){
                $childrenRows[$cheapestChildIndex][$feedHeaderToIndexes['link']] = $this->addParamToLink($cheapestChildLink, "alt=".$currentAlt);
            }

            foreach($childrenRows as $childProductFeedRow){
                if(isset($childProductFeedRow[$feedHeaderToIndexes['id']]) && $childProductFeedRow[$feedHeaderToIndexes['id']]){
                    $feedRows[] = $this->addOrderKeysAndCellsByOrder($childProductFeedRow);
                }
            }

            $addedProductIds[] = $product->getId();
            $numOfProcessedProducts++;

            $feedProductsNumberStatusMessage = "Processing product ".$numOfProcessedProducts." out of ".$numOfProductsToProcess."\n";

            $skippedConfigurableCount++;

            $this->appendToProgressFile($feedProductsNumberStatusMessage);
        }

        $this->setLastAlt($currentAlt);

        $productsCollection = $this->productCollectionFactory->create();
        $productsCollection
            ->addAttributeToFilter("type_id", ['neq' => \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE])
            ->addFieldToFilter('status', ProductStatus::STATUS_ENABLED)
            ->addStoreFilter($store)
            ->addPriceData($this->getCustomerGroupIdForTax($store), $store->getWebsite()->getId());

        if(!$this->getIsAllProductsInFeed($store)){
            $productsCollection->addFieldToFilter('is_in_fb_feed', \Magento\Eav\Model\Entity\Attribute\Source\Boolean::VALUE_YES);
        }

        if(!$this->getIsIncludeNonvisibleProducts($store)){
            $productsCollection->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds());
        }

        foreach($productsCollection as $product){
            if(in_array($product->getId(), $addedProductIds)){
                continue;
            }

            if($this->getRemoveOutOfStockProductsFromFeed($store)){
                if($this->getStockStatus($product, $store) != \Magento\CatalogInventory\Model\Stock::STOCK_IN_STOCK){
                    continue;
                }
            }

            /**
             * @var \Magento\Catalog\Model\Product $product
             */
            $product = $this->productFactory->create()->setStoreId($store->getId())->load($product->getId());

            try{
                $productFeedRow = $this->getProductFeedRow($product, $feedHeader, $fbFeedId, $store);

                $customOptionsVariationRows = $this->getCustomOptionsVariationRows($product, $productFeedRow, $feedHeader, $store, $this->getAddCustomOptions($store));

                if(count($customOptionsVariationRows)){
                    $orderedCustomOptionsVariationRows =
                        array_map(
                            function($productFeedRow){
                                return $this->addOrderKeysAndCellsByOrder($productFeedRow);
                            },
                            $customOptionsVariationRows
                        );
                    $feedRows = array_merge($feedRows, $orderedCustomOptionsVariationRows);
                }else{
                    $feedRows[] = $this->addOrderKeysAndCellsByOrder($productFeedRow);
                }
            }catch(\Exception $e){
                $feedProductsNumberStatusMessage = sprintf("Skipping product (ID %s), do to an error: %s\n", $product->getId(), $e->getMessage());
                $this->appendToProgressFile($feedProductsNumberStatusMessage);
            }

            $numOfProcessedProducts++;
            $addedProductIds[] = $product->getId();

            $feedProductsNumberStatusMessage = "Processing product ".$numOfProcessedProducts." out of ".$numOfProductsToProcess."\n";
            $this->appendToProgressFile($feedProductsNumberStatusMessage);
        }

        $this->appendToProgressFile("Finished rows creation...\n");

        return [
            'added_product_ids' => $addedProductIds,
            'feed_rows' => $feedRows,
            'skipped_configurable_products_count' => $skippedConfigurableCount,
            'processed_products_count' => $numOfProcessedProducts
        ];
    }

    public function getFeedFileName($storeCode){
        return self::FEED_FILE_NAME."_".$storeCode.".".self::FEED_FILE_EXTENSION;
    }

    public function getFeedFileFullPath($storeCode){
        $store = $this->storeManager->getStore($storeCode);
        if($this->getFeedFileLocation($store) == self::FEED_FILE_LOCATION_ROOT){
            return $this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::ROOT)->getAbsolutePath().self::FEED_FILE_NAME."_".$storeCode.".".self::FEED_FILE_EXTENSION;
        }else{
            return $this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::PUB)->getAbsolutePath()."static/".self::FEED_FILE_NAME."_".$storeCode.".".self::FEED_FILE_EXTENSION;
        }
    }

    protected function writeFeed($feedData, $storeCode){
        $this->csvParser->saveData(
            $this->getFeedFileFullPath($storeCode),
            $feedData
        );
    }

    protected function getFeedHeader(){
        return self::$fields;
    }

    public function getProductAttributeIdByCode($attributeCode){
        return $this->eavAttribute->getIdByCode(\Magento\Catalog\Model\Product::ENTITY, $attributeCode);
    }

    public function getStoreBaseUrl(){
        return $productUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
    }

    public function removeProgressFile(){
        return $this->file->rm($this->getProgressFileFullPath());
    }

    public function getProgressFileUrl(){
        return $this->getStoreBaseUrl().self::PROGRESS_FILE_NAME;
    }

    public function getProductUrl($product, $store,$fbVariationFieldsToOptionIds=[],){
        $isTrackingEnabled = $this->getIsTrackingEnabled($store);
        $whereToRedirectFromFacebook = $this->getWhereToRedirectFromFacebook($store);

        if(in_array($product->getTypeId(), [
                \Magento\Bundle\Model\Product\Type::TYPE_CODE,
                \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE
            ])
            || (($whereToRedirectFromFacebook == \Mexbs\Fbshop\Model\Config\Source\ProductRedirect::USE_CONFIG_IN_PRODUCT) && !$product->getIsFbRedirectsToCheckout())
            || ($whereToRedirectFromFacebook == \Mexbs\Fbshop\Model\Config\Source\ProductRedirect::REDIRECT_TO_PRODUCT_PAGE)

        ){
            $productUrl = $this->fbProductUrl->getProductUrlForStore($product, $store);
        }else{
            $productUrl = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB)."fbshop/checkout/index/";
            $productUrl .= "product_id/".$product->getId()."/";
            foreach($fbVariationFieldsToOptionIds as $fbField => $optionId){
                $productUrl .= $fbField."/".$optionId."/";
            }
            $productUrl .= "store_id/".$store->getId();
        }

        if($isTrackingEnabled){
            $productUrl = $this->addParamToLink($productUrl, self::FROMFB_PARAM_NAME ."=1");
        }

        return $productUrl;
    }

    public function getProductUrlWithCustomOptions($product, $customOptionIdsToValueIds, $store){
        $isTrackingEnabled = $this->getIsTrackingEnabled($store);
        $whereToRedirectFromFacebook = $this->getWhereToRedirectFromFacebook($store);
        if(in_array($product->getTypeId(), [
                \Magento\Bundle\Model\Product\Type::TYPE_CODE,
                \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE
            ])
            || (($whereToRedirectFromFacebook == \Mexbs\Fbshop\Model\Config\Source\ProductRedirect::USE_CONFIG_IN_PRODUCT) && !$product->getIsFbRedirectsToCheckout())
            || ($whereToRedirectFromFacebook == \Mexbs\Fbshop\Model\Config\Source\ProductRedirect::REDIRECT_TO_PRODUCT_PAGE))
        {
            $productUrl = $this->fbProductUrl->getProductUrlForStore($product, $store);
        }else{
            $productUrl = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB)."fbshop/checkout/addwithcustomoptions/";
            $productUrl .= "product_id/".$product->getId()."/";
            foreach($customOptionIdsToValueIds as $optionId => $optionValueId){
                $productUrl .= $optionId."/".$optionValueId."/";
            }
            $productUrl .= "store_id/".$store->getId();
        }

        if($isTrackingEnabled){
            $productUrl = $this->addParamToLink($productUrl, self::FROMFB_PARAM_NAME ."=1");
        }

        return $productUrl;
    }

    /**
     * @param string $triggeredBy
     * @param \Magento\Store\Model\Store $store
     * @throws \Exception
     */
    public function generateFeed($store, $triggeredBy = "cron"){
        $feedRowsAndAddedProductIds = [];

        $message = "";
        $startedAtTimestamp = $this->date->gmtTimestamp();

        $status = self::STATUS_SUCCESS;

        try{
            if($this->getIsGenerationInProgress()){
                throw new \Exception("Feed generation is already in progress. Please try again later.");
            }

            $this->setGenerationInProgress();

            $this->writeToProgressFile(sprintf("Starting feed generation for store (%s)\n", $store->getCode()));
            $fbFeedId = $this->getFeedId($store);
            if(!$fbFeedId && $this->getIsAppendFeedIdToProductId($store)){
                throw new \Exception('Please set up the feed ID in Stores -> Configuration -> General -> Facebook Shop Integration or set , or set the configuration "Append the Feed ID to the product IDs" to "No"');
            }

            $missingMappingsErrorMessage = $this->getMissingFieldsErrorMessage();
            if($missingMappingsErrorMessage != ""){
                throw new \Exception($missingMappingsErrorMessage);
            }

            $feedHeader = $this->getFeedHeader();
            $this->appendToProgressFile("Starting feed rows generation...\n");
            $feedRowsAndAddedProductIds = $this->getFeedRows($feedHeader, $fbFeedId, $store);
            $feedData = array_merge([$feedHeader], $feedRowsAndAddedProductIds['feed_rows']);
            if($feedRowsAndAddedProductIds['skipped_configurable_products_count'] > 0){
                $this->appendToProgressFile(sprintf("Important: the rows include %s configurable products, they WON'T be added to the feed, but only their children. This is because Facebook doesn't use parent products, only the children (variations) of the configurable products.\n", $feedRowsAndAddedProductIds['skipped_configurable_products_count']));
            }

            $this->appendToProgressFile(sprintf("Processed %s products. Added to Facebook feed %s products.\n", $feedRowsAndAddedProductIds["processed_products_count"], ($feedRowsAndAddedProductIds["processed_products_count"] - $feedRowsAndAddedProductIds['skipped_configurable_products_count'])));
            if(count($feedData) == 1){
                $this->appendToProgressFile("There is no products in the file (only a header) - probably no products have been assigned to the feed!\n");
            }
            $this->appendToProgressFile(sprintf("Writing feed to file (%s)...\n", $this->getFeedFileFullPath($store->getCode())));
            $this->writeFeed($feedData, $store->getCode());
            $this->appendToProgressFile("Finished!");

            $message .= sprintf("The feed file (%s) was generated successfully.\n", $this->getFeedFileFullPath($store->getCode()));
            $this->unsetGenerationInProgress();

            $finishedAtTimestamp = $this->date->gmtTimestamp();

            $addedProductIds = [];
            if(is_array($feedRowsAndAddedProductIds['added_product_ids'])){
                $addedProductIds = $feedRowsAndAddedProductIds['added_product_ids'];
            }
            sort($addedProductIds);

            $this->feedLogFactory->create()
                ->setStoreCode($store->getCode())
                ->setTriggeredBy($triggeredBy)
                ->setProductIds(substr(implode(", ",$addedProductIds), 0 , 100000))
                ->setMessage($message)
                ->setStatus($status)
                ->setStartedAt($startedAtTimestamp)
                ->setFinishedAt($finishedAtTimestamp)
                ->save();

        }catch(\Exception $e){
            $status = self::STATUS_ERROR;
            $this->appendToProgressFile($e->getMessage()."\n");
            $message = sprintf("The following error has occurred: %s", $e->getMessage());

            $finishedAtTimestamp = $this->date->gmtTimestamp();

            $this->feedLogFactory->create()
                ->setTriggeredBy($triggeredBy)
                ->setStoreCode($store->getCode())
                ->setProductIds(substr(implode(", ",(isset($feedRowsAndAddedProductIds['added_product_ids']) ? $feedRowsAndAddedProductIds['added_product_ids'] : [])), 0 , 100000))
                ->setMessage($message)
                ->setStatus($status)
                ->setStartedAt($startedAtTimestamp)
                ->setFinishedAt($finishedAtTimestamp)
                ->save();

            $this->unsetGenerationInProgress();

            throw $e;
        }
    }

    public function getDateFromTimestamp($timestamp){
        return $this->date->gmtDate(null, $timestamp);
    }

    public function getIsFeedFileExists($storeCode){
        return $this->file->fileExists($this->getFeedFileFullPath($storeCode));
    }

    public function getFbImagePath(){
        return $this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath() .'catalog/product/fb/';
    }

    public function getProductImagePath(){
        return $this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath() .'catalog/product/';
    }

    public function getFbImageUrl(){
        return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product/fb';
    }

    public function recreateFbImage($baseImageSubPath, $baseImageName){
        $fbImage = $this->imageFactory->create($this->getProductImagePath() . $baseImageSubPath . '/' . $baseImageName);

        $fbImage->keepTransparency(true);
        $fbImage->constrainOnly(true);
        $fbImage->keepFrame(true);
        $fbImage->keepAspectRatio(true);
        $fbImage->backgroundColor([255, 255, 255]);

        $biggestDimension = ($fbImage->getOriginalWidth() > $fbImage->getOriginalHeight() ? 'width' : 'height');
        if($biggestDimension == 'width'){
            $fbImage->resize($fbImage->getOriginalWidth(), $fbImage->getOriginalWidth());
        }else{
            $fbImage->resize($fbImage->getOriginalHeight(), $fbImage->getOriginalHeight());
        }

        $fbImage->save($this->getFbImagePath(). $baseImageSubPath, $baseImageName);

        return $this;
    }

    public function setFbTrackingCookie(){
        $this->cookieManager->setPublicCookie(
            self::FB_COOKIE_NAME,
            '1',
            $this->cookieMetadataFactory->createPublicCookieMetadata()
                ->setHttpOnly(true)
                ->setDuration(60*60*24*90) //90 days
                ->setPath($this->storeManager->getStore()->getStorePath())
        );
    }
}
