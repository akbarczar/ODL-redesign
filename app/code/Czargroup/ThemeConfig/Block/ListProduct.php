<?php
/**
 * @category   Czargroup
 * @package    Czargroup_ThemeConfig
 * @author     Czargroup Technologies <info@czargroup.net>
 * @copyright  Copyright (c) Czargroup Technologies
 * @license    https://www.czargroup.net/license
 */

declare(strict_types=1);

namespace Czargroup\ThemeConfig\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;

/**
 * Class ListProduct
 * Provides product price formatting for the frontend.
 */
class ListProduct extends Template
{
    /**
     * @var PriceHelper
     */
    protected PriceHelper $priceHelper;

    /**
     * Constructor
     *
     * @param Template\Context $context
     * @param PriceHelper $priceHelper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        PriceHelper $priceHelper,
        array $data = []
    ) {
        $this->priceHelper = $priceHelper;
        parent::__construct($context, $data);
    }

    /**
     * Get formatted product price.
     *
     * @param float $price
     * @return string
     */
    public function getFormattedPrice(float $price): string
    {
        return $this->priceHelper->currency($price, true, false);
    }
}
