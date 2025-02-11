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

namespace Ksolves\LayeredNavigation\Plugin\CatalogSearch\Result;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\ViewInterface;

/**
 * class Index
 */
class Index
{
    /**
     * @var JsonFactory
     */
    protected $ksResultJsonFactory;

    /**
     * @var ViewInterface
     */
    protected $ksView;

    /**
     * @param ViewInterface $ksView
     * @param JsonFactory $ksResultJsonFactory
     */
    public function __construct(
        ViewInterface $ksView,
        JsonFactory $ksResultJsonFactory
    ) {
        $this->ksView = $ksView;
        $this->ksResultJsonFactory = $ksResultJsonFactory;
    }

    /**
     * Search result action
     *
     * @param  \Magento\CatalogSearch\Controller\Result\Index $ksAction
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function aroundExecute(
        \Magento\CatalogSearch\Controller\Result\Index $ksAction,
        \Closure $ksProceed
    ) {
        if ($ksAction->getRequest()->isAjax()) {
            $ksProceed();
            $ksResult = [
                    'products' => $this->ksView->getLayout()->getBlock('search.result')->toHtml(),
                    'sidebar_main' => $this->ksView->getLayout()->getBlock('catalogsearch.leftnav')->toHtml()
                ];
            $ksResultJson = $this->ksResultJsonFactory->create();
            return $ksResultJson->setData($ksResult);
        } else {
            return $ksProceed();
        }
    }
}
