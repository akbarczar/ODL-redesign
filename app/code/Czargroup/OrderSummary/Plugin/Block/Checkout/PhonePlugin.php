<?php

/**
 * Copyright (c) 2025 Czargroup Technologies. All rights reserved.
 *
 * @package Czargroup_ImportTracknumber
 * @author Czargroup Technologies
 */

namespace Czargroup\OrderSummary\Plugin\Block\Checkout;

use Magento\Checkout\Block\Checkout\AttributeMerger;

class PhonePlugin
{
    /**
     * @param AttributeMerger $subject
     * @param $result
     * @return mixed
     */
    public function afterMerge(AttributeMerger $subject, $result)
    {
        $result['telephone']['validation'] = [
            'required-entry'  => true,
            'max_text_length' => 11,
            'validate-number' => true
        ];
        return $result;
    }
}
