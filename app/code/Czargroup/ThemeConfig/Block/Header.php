<?php
namespace Czargroup\ThemeConfig\Block;

use Magento\Framework\View\Element\Template;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\UrlInterface;
use Magento\Customer\Model\Session as CustomerSession;

class Header extends Template
{
    protected $storeManager;
    protected $scopeConfig;
    protected $customerSession;

    public function __construct(
        Template\Context $context,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        CustomerSession $customerSession,
        array $data = []
    ) {
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->customerSession = $customerSession;
        parent::__construct($context, $data);
    }

    /**
     * Get Logo URL
     */
    public function getLogoUrl()
    {
        $logoPath = $this->scopeConfig->getValue('design/header/logo_src', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . 'logo/' . $logoPath;
    }

    /**
     * Get Logo Alt Text
     */
    public function getLogoAlt()
    {
        return $this->scopeConfig->getValue('design/header/logo_alt', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Store Base URL
     */
    public function getHomeUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl();
    }

    /**
     * Get Search Bar
     */
    public function getSearchBarHtml()
    {
        return $this->getLayout()->createBlock('Magento\Framework\View\Element\Template')
            ->setTemplate('Magento_Search::form.mini.phtml')
            ->toHtml();
    }

    /**
     * Get My Account Links
     */
    public function getAccountUrl()
    {
        return $this->getUrl('customer/account');
    }

    public function getLoginUrl()
    {
        return $this->getUrl('customer/account/login');
    }

    public function getLogoutUrl()
    {
        return $this->getUrl('customer/account/logout');
    }

    public function getWishlistUrl()
    {
        return $this->getUrl('wishlist/');
    }

    public function getCompareUrl()
    {
        return $this->getUrl('catalog/product_compare/index');
    }

    /**
     * Check if customer is logged in
     */
    public function isLoggedIn()
    {
        return $this->customerSession->isLoggedIn();
    }
}
