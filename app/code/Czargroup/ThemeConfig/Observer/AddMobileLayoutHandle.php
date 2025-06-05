<?php
namespace Czargroup\ThemeConfig\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\App\Request\Http;

class AddMobileLayoutHandle implements ObserverInterface
{
    protected $request;

    public function __construct(Http $request)
    {
        $this->request = $request;
    }

    public function execute(Observer $observer)
    {
        if (preg_match('/Mobile|Android|iPhone|iPad|iPod/i', $this->request->getServer('HTTP_USER_AGENT'))) {
            $layout = $observer->getEvent()->getLayout();
            $layout->getUpdate()->addHandle('catalogsearch_result_index_mobile');
        }
    }
}
?>