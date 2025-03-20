<?php

/**
 * Copyright (c) 2025 Czargroup Technologies. All rights reserved.
 *
 * @package Czargroup_ImportTracknumber
 * @author Czargroup Technologies
 */

namespace Czargroup\OrderSummary\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Shipping\Model\Config;

class Display extends Template
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var Config
     */
    protected $shipconfig;

    /**
     * Display constructor.
     *
     * @param Template\Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param Config $shipconfig
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        ScopeConfigInterface $scopeConfig,
        Config $shipconfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->shipconfig = $shipconfig;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Retrieve the list of available shipping methods.
     *
     * @return array List of active shipping methods with carrier titles.
     */
    public function getShippingMethods()
    {
        $activeCarriers = $this->shipconfig->getActiveCarriers();
        $methods = [];

        foreach ($activeCarriers as $carrierCode => $carrierModel) {
            $options = [];

            if ($carrierMethods = $carrierModel->getAllowedMethods()) {
                foreach ($carrierMethods as $methodCode => $method) {
                    $code = $carrierCode . '_' . $methodCode;
                    $options[] = ['value' => $code, 'label' => $method];
                }
                $carrierTitle = $this->scopeConfig->getValue('carriers/' . $carrierCode . '/title');
            }

            $methods[] = ['value' => $options, 'label' => $carrierTitle];
        }

        return $methods;
    }
}
