<?php

namespace Shiptheory\Shippingx\Model;

use GuzzleHttp\ClientFactory;
use GuzzleHttp\Exception\GuzzleException;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\IntegrationException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Integration\Api\IntegrationServiceInterface;
use Magento\Integration\Api\OauthServiceInterface;
use Magento\Integration\Model\Integration;
use Magento\Store\Model\StoreManagerInterface;
use Magento\User\Model\UserFactory;
use Psr\Log\LoggerInterface;
use Shiptheory\Shippingx\Api\ApiInterface;

class Api implements ApiInterface
{
    /**
     * @var UserFactory
     */
    private $userFactory;

    /**
     * @var IntegrationServiceInterface
     */
    private $integrationService;

    /**
     * @var OauthServiceInterface
     */
    private $oauthService;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ClientFactory
     */
    private $clientFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    const API_URL       = 'https://helm.shiptheory.com/magento2/setup';
    const API_USER_NAME = 'shiptheory';
    const API_EMAIL     = 'shiptheory@shiptheory.com';

    const API_RESOURCES = [
        '',
        'Magento_Backend::admin',
        'Magento_Sales::sales',
        'Magento_Sales::sales_operation',
        'Magento_Sales::comment',
        'Magento_Sales::actions_view',
        'Magento_Sales::shipment',
        'Magento_User::acl_users',
        'Magento_Catalog::catalog',
        'Magento_Catalog::catalog_inventory',
        'Magento_Catalog::products',
        'Shiptheory_Shippingx::shiptheory',
        'Shiptheory_Shippingx::shiptheory_config',
        'Shiptheory_Shippingx::shiptheory_history',

    ];

    /**
     * Api constructor.
     * @param UserFactory $userFactory
     * @param IntegrationServiceInterface $integrationService
     * @param OauthServiceInterface $oauthService
     * @param LoggerInterface $logger
     * @param ClientFactory $clientFactory
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        UserFactory $userFactory,
        IntegrationServiceInterface $integrationService,
        OauthServiceInterface $oauthService,
        LoggerInterface $logger,
        ClientFactory $clientFactory,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->userFactory = $userFactory;
        $this->integrationService = $integrationService;
        $this->oauthService = $oauthService;
        $this->logger = $logger;
        $this->scopeConfig = $scopeConfig;
        $this->clientFactory = $clientFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * @param $websiteId
     * @throws IntegrationException|GuzzleException
     */
    public function createIntegration($websiteId)
    {
        $client = $this->clientFactory->create([
            'config' => [
                'verify' => false,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ]
            ]
        ]);

        $apiKey = $this->scopeConfig->getValue("shiptheory/setting/api_key", \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE, $websiteId);
        $integration = $this->integrationService->create([
            'name'      => self::API_USER_NAME,
            'email'     => self::API_EMAIL,
            'resource'  => self::API_RESOURCES,
            'status'    => Integration::STATUS_ACTIVE,
            'endpoint'  => self::API_URL
        ]);

        try {
            $this->oauthService->createAccessToken($integration->getConsumerId());
            $storeBaseUrl = $this->storeManager->getStore()->getBaseUrl();
            $client->post(self::API_URL, [
                'json' => [
                    'oauth_access_token' => $this->oauthService->getAccessToken($integration->getConsumerId())->getToken(),
                    'apikey' => $apiKey,
                    'url' => $storeBaseUrl,
                ],
            ]);
        } catch (LocalizedException $e) {
            $this->logger->critical($e);
        }
    }

    public function removeIntegration()
    {
        try {
            $integration = $this->integrationService->findByName(self::API_USER_NAME);
            $this->integrationService->delete($integration->getId());
        } catch (IntegrationException $e) {
            $this->logger->critical($e);
        }
    }
}
