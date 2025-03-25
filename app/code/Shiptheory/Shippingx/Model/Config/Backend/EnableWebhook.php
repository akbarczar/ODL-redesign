<?php

namespace Shiptheory\Shippingx\Model\Config\Backend;

use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Shiptheory\Shippingx\Api\ApiInterface;
use Shiptheory\Shippingx\Model\ApiFactory;

class EnableWebhook extends Value
{
    /**
     * @var ApiInterface
     */
    private $apiInterface;

    /**
     * EnableWebhook constructor.
     * @param Context $context
     * @param Registry $registry
     * @param ScopeConfigInterface $config
     * @param TypeListInterface $cacheTypeList
     * @param ApiInterface $apiInterface
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        ApiInterface $apiInterface,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->apiInterface = $apiInterface;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    public function beforeSave()
    {
        $websiteId = $this->getWebsiteId($this->getScopeId(), $this->getScope());

        if ($this->getValue() == 1) {
            $this->apiInterface->createIntegration($websiteId);
        }

        if ($this->getValue() == 0) {
            $this->apiInterface->removeIntegration($websiteId);
        }

        $this->setValue($this->getValue());

        parent::beforeSave();
    }

    /**
     * @param $scopeId
     * @param $scope
     * @return int
     */
    protected function getWebsiteId($scopeId, $scope)
    {
        switch ($scope) {
            case 'default':
                return 0;
            case 'websites':
                return $scopeId;
        }
    }
}
