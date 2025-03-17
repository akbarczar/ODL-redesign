<?php
namespace Mexbs\Fbshop\Model\Config\Source;

class ProductRedirect implements \Magento\Framework\Option\ArrayInterface
{
    const USE_CONFIG_IN_PRODUCT = 0;
    const REDIRECT_TO_CHECKOUT = 1;
    const REDIRECT_TO_PRODUCT_PAGE = 2;

    public function toOptionArray()
    {
        return [
            ['value' => self::USE_CONFIG_IN_PRODUCT, 'label' => __('Get the configuration from the product')],
            ['value' => self::REDIRECT_TO_CHECKOUT, 'label' => __('Redirect to checkout')],
            ['value' => self::REDIRECT_TO_PRODUCT_PAGE, 'label' => __('Redirect to the product page')]
        ];
    }
}
