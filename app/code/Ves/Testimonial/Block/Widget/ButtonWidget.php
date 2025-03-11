<?php
/**
 * Venustheme
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://venustheme.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category  Venustheme
 * @package   Ves_Testimonial
 * @copyright Copyright (c) 2017 Landofcoder (http://www.venustheme.com/)
 * @license   http://www.venustheme.com/LICENSE-1.0.html
 */

namespace Ves\Testimonial\Block\Widget;

use Magento\Customer\Model\Session as CustomerSession;

class ButtonWidget extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
    /**
     * @var \Ves\Testimonial\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;


    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Ves\Testimonial\Helper\Data $_helper,
        CustomerSession $customerSession,
        array $data = []
    ) {
        $this->_helper   = $_helper;
        $this->urlHelper = $urlHelper;
        $this->customerSession = $customerSession;
        parent::__construct($context, $data);

    }//end __construct()


    protected function _construct()
    {
        parent::_construct();

    }//end _construct()


    public function _toHtml()
    {
        $enable = $this->_helper->getConfig('general/enable');
        if(!$enable) { return;
        }
        $enable_form = $this->_helper->getConfig('general/enable_form');
        if(!$enable_form) { return;
        }
        $require_loggedin = $this->_helper->getConfig('general/form_require_customer');
        if($require_loggedin && !$this->customerSession->getCustomerGroupId() ) {
            return;
        }
        $template = 'Ves_Testimonial::widget/button_widget.phtml';
        $this->setTemplate($template);
        return parent::_toHtml();

    }//end _toHtml()


    public function getConfig($key, $default = '')
    {
        if($this->hasData($key) && $this->getData($key)) {
            return $this->getData($key);
        }

        return $default;

    }//end getConfig()


}//end class
