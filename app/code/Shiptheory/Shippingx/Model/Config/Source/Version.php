<?php

namespace Shiptheory\Shippingx\Model\Config\Source;

use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Module\ModuleList;
use Magento\Framework\Module\ResourceInterface;
use Magento\Framework\Registry;

/**
 * Config backend model for version display.
 */
class Version extends Value
{
    /**
     * @var ResourceInterface
     */
    protected $moduleResource;

    /**
     * @var ModuleList
     */
    private $moduleList;

    /**
     * Version constructor.
     * @param Context $context
     * @param Registry $registry
     * @param ScopeConfigInterface $config
     * @param TypeListInterface $cacheTypeList
     * @param ResourceInterface $moduleResource
     * @param ModuleList $moduleList
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        ResourceInterface $moduleResource,
        ModuleList $moduleList,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $config,
            $cacheTypeList,
            $resource,
            $resourceCollection,
            $data
        );

        $this->moduleResource = $moduleResource;
        $this->moduleList = $moduleList;
    }

    /**
     * Inject current installed module version as the config value.
     *
     * @return void
     */
    public function afterLoad()
    {
        $version = $this->moduleList->getOne('Shiptheory_Shippingx');
        $this->setValue($version['setup_version']);
    }
}
