<?php 
/**
 * Copyright (c) 2025 Czargroup Technologies. All rights reserved.
 *
 * @package Czargroup_Cashondeliveryadmin
 * @author Czargroup Technologies
 */
namespace Czargroup\Cashondeliveryadmin\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\ObjectManager;

class ObservertoDisablecod implements ObserverInterface
{
    /**
    * @var \Magento\Framework\App\State
    */
    protected $_appState;

    /**
    * Constructor
    * 
    * @param \Magento\Framework\App\State $appState
    */
    public function __construct(\Magento\Framework\App\State $appState)
    {
        $this->_appState = $appState;
    }

    /**
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
		$result = $observer->getEvent()->getResult();
		$method_instance = $observer->getEvent()->getMethodInstance();
		$quote = $observer->getEvent()->getQuote();		
        if(null !== $quote)
        {
            if($method_instance->getCode() == 'cashondelivery' && in_array($this->_appState->getAreaCode(), $this->getDisableAreas()))
            {
                $result->setData('is_available',false);   
            }
        }
    }

    /**
     * Returns the areas where the COD payment method should be disabled.
     *
     * @return string[]
     */
    protected function getDisableAreas()
    {
        return [
            \Magento\Framework\App\Area::AREA_FRONTEND, 
            \Magento\Framework\App\Area::AREA_WEBAPI_REST
        ];
    }
}