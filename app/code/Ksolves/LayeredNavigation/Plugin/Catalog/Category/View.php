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

namespace Ksolves\LayeredNavigation\Plugin\Catalog\Category;

use Magento\Framework\Controller\Result\JsonFactory;

/**
 * class View
 */
class View
{
    /**
     * @var JsonFactory
     */
    protected $ksResultJsonFactory;

    /**
     * @param JsonFactory $ksResultJsonFactory
     */
    public function __construct(
        JsonFactory $ksResultJsonFactory
    ) {
        $this->ksResultJsonFactory = $ksResultJsonFactory;
    }

    /**
     * Category view action
     *
     * @param  \Magento\Catalog\Controller\Category\View $ksAction
     * @param  \Closure $ksProceed
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function aroundExecute(\Magento\Catalog\Controller\Category\View $ksAction, \Closure $ksProceed)
    {
        if ($ksAction->getRequest()->isAjax()) {
            $ksPage = $ksProceed();
            $ksResult =
                [
                    'products'      => $ksPage->getLayout()->getBlock('category.products')->toHtml(),
                    'sidebar_main'  => $ksPage->getLayout()->getBlock('catalog.leftnav')->toHtml()
                ];
            $ksResultJson = $this->ksResultJsonFactory->create();
            return $ksResultJson->setData($ksResult);
        } else {
            return $ksProceed();
        }
    }
}
