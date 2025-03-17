<?php
namespace Mexbs\Fbshop\Controller\Download;


class Feed extends \Magento\Framework\App\Action\Action
{
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Mexbs\Fbshop\Helper\Data $helper
    ){
        $this->helper = $helper;
        parent::__construct($context);
    }

    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $storeCode = $params['store_code'];
		
		$storeCode = preg_replace("/[^a-zA-Z0-9\-_]/", "", $storeCode);

        $fileName = $this->helper->getFeedFileName($storeCode);
        $file = $this->helper->getFeedFileFullPath($storeCode);

        if(!file_exists($file)){
            $this->messageManager->addErrorMessage('Feed file not found');
            return;
        } else {
            header("Cache-Control: public");
            header("Content-Description: File Transfer");
            header("Content-Disposition: attachment; filename=$fileName");
            header("Content-Type: application/zip");
            header("Content-Transfer-Encoding: binary");

            readfile($file);
        }
        // phpcs:ignore Magento2.Security.LanguageConstruct.ExitUsage
        exit(0);
    }
}