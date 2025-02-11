<?php
/**
* Ksolves
*
* @category  Ksolves
* @package   Ksolves_LayeredNavigation
* @author    Ksolves Team
* @copyright Copyright (c) Ksolves India Limited (https://www.ksolves.com/)
* @license   https://store.ksolves.com/magento-license
*/

namespace Ksolves\LayeredNavigation\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

/**
 * Deprecated!!!
 * Ksolves Layered Navigation Config Helper
 */
class Data extends AbstractHelper
{
    public const KSOLVES_CONFIG_MODULE_PATH = 'ks_layeredNavigation/';

    /**
     * @param $ksField
     * @param null $ksStoreId
     * @return array|mixed
     */
    public function getConfigValue($ksField, $ksStoreId = null)
    {
        return $this->scopeConfig->getValue(
            $ksField,
            ScopeInterface::SCOPE_STORE,
            $ksStoreId
        );
    }

    /**
     * @param string $ksCode
     * @param null $ksStoreId
     * @return mixed
     */
    public function getConfigGeneral($ksCode, $ksStoreId = null)
    {
        return $this->getConfigValue(self::KSOLVES_CONFIG_MODULE_PATH .'general/'. $ksCode, $ksStoreId);
    }

    /**
     *  check ajax is enabled / disabled
     * @param null $ksStoreId
     * @return bool
     */
    public function isAjaxEnabled()
    {
        return $this->getConfigGeneral('ks_enable_ajax');
    }

    /**
     *  check price slider is enabled / disabled
     * @param null $ksStoreId
     * @return bool
     */
    public function getPriceSlider()
    {
        return $this->getConfigGeneral('ks_use_slider');
    }
}
