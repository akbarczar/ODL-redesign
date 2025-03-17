<?php
/**
 * Copyright (c) 2025 Czargroup Technologies. All rights reserved.
 *
 * @package Czargroup_Customform
 * @author Czargroup Technologies
 */
namespace Czargroup\Customform\Controller\Index;


use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;


class Index extends Action
{
    private $dataPersistor;
    /**
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\View\Result\Page
     */

    protected $context;
    private $fileUploaderFactory;
    private $fileSystem;


    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */


    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */

     public function __construct(
        \Magento\Framework\App\Action\Context $context,
        Filesystem $fileSystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
       parent::__construct($context);
        $this->fileUploaderFactory = $fileUploaderFactory;
        $this->fileSystem          = $fileSystem;
        $this->_transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
         $this->scopeConfig = $scopeConfig;
    }

    public function execute()
    {

        $post = $this->getRequest()->getPostValue();
        $fileNames = array('attachment','attachment_1','attachment_2');
       // $fileNames = array('attachment');
		$writer = new \Zend_Log_Writer_Stream(BP . '/var/log/acontact.log');
		$logger = new \Zend_Log();
		$logger->addWriter($writer);
		$logger->info('Test the loggggg');
         $fileName=array();
         $filePath=array();
         $i = 0;
		 $filetype=array();
       foreach($fileNames as $_fileName) {
         
        $filesData = $this->getRequest()->getFiles($_fileName);

       $filesData = $this->getRequest()->getFiles($_fileName);

if (isset($filesData['name']) && !empty($filesData['name'])) {
    $filetype[$i] = $filesData['type'];
    $uploader = $this->fileUploaderFactory->create(['fileId' => $_fileName]);
    $uploader->setAllowRenameFiles(true);
    $uploader->setFilesDispersion(true);
    $uploader->setAllowCreateFolders(true);
    $uploader->setAllowedExtensions(['jpg', 'png', 'jpeg', 'gif', 'doc', 'docx', 'xls', 'xlsx', 'pdf', 'zip']);
    $path = $this->fileSystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('test-doc');

    $result = $uploader->save($path);
    $upload_document = 'test-doc' . $uploader->getUploadedFilename();
    $filePath[$i] = $result['path'] . $result['file'];
    $fileName[$i] = $result['name'];
} else {
    $upload_document = '';
    $filePath[$i] = '';
    $fileName[$i] = '';
    $filetype[$i] = '';
}
	   $i++;}

        $customerName=$post['name'];
        //$userSubject= 'Outdoorliving Customer Query';     
        $userSubject= $post['reasonforcontact'];     
        
		//$toemail = 'nrupa@czargroup.net';addBcc('nrupa@czargroup.net')
		//$toemail = $this->scopeConfig->getValue(\Magento\Contact\Controller\Index\Post::XML_PATH_EMAIL_RECIPIENT, ScopeInterface::SCOPE_STORE);
		$toemail = "akbar@czargroup.net";
		$toemail = "support@outdoorlivinguk.co.uk";
		 /* $fromEmail = $this->scopeConfig->getValue(\Magento\Contact\Controller\Index\Post::XML_PATH_EMAIL_SENDER, ScopeInterface::SCOPE_STORE); */
		//$fromEmail = $this->scopeConfig->getValue('trans_email/ident_general/email', ScopeInterface::SCOPE_STORE);
		$fromEmail = 'sales@outdoorlivinguk.co.uk';
        //$fromName  = 'Outdoorliving Contact us';
        $fromName  = $post['name'].'_'.$post['email'];
		$replyToEmail = $post['email'];
		$replyToName = $post['name'];
$logger->info(print_r($post, true));
$logger->info("replyToEmail:" .$replyToEmail);
$logger->info("fromName:" .$fromName);

		
        $templateVars = [
            'customer_name' => $customerName,
                    'subject' => $userSubject,
                    'name'   => $post['name'],
                    'email'   => $post['email'],
                    'telephone'   => $post['phone_number'],
                     'orderid'   => $post['order_number'] ,
					  'reasonforcontact'   => $post['reasonforcontact'],
					  'purchase_location'   => $post['purchase_location'], 
					 'comment'   => $post['message']
                    ];
        $from = ['email' => $fromEmail, 'name' => $fromName];
        $this->inlineTranslation->suspend();
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

         $templateOptions = [
          'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
          'store' => 1
        ];
			if ((!empty($filePath[0]) && file_exists($filePath[0]))&&(!empty($filePath[1]) && file_exists($filePath[1]))&&(!empty($filePath[2]) && file_exists($filePath[2]))&&(!empty($filePath[3]) && file_exists($filePath[3]))&&(!empty($filePath[4]) && file_exists($filePath[4]))) {
				$transport = $this->_transportBuilder->setTemplateIdentifier('customform_form_template')
                ->setTemplateOptions($templateOptions)
                ->setTemplateVars($templateVars)
                ->setFrom($from)
                ->addTo($toemail)
				->setReplyTo($replyToEmail, $replyToName)
				->addAttachment(file_get_contents($filePath[0]), $fileName[0],$filetype[0])
				->addAttachment(file_get_contents($filePath[1]), $fileName[1],$filetype[1])
				->addAttachment(file_get_contents($filePath[2]), $fileName[2],$filetype[2])
				->addAttachment(file_get_contents($filePath[3]), $fileName[3],$filetype[3])
				->addAttachment(file_get_contents($filePath[4]), $fileName[4],$filetype[4])
				->getTransport();
				$transport->sendMessage();
			}
			else if ((!empty($filePath[0]) && file_exists($filePath[0]))&&(!empty($filePath[1]) && file_exists($filePath[1]))&&(!empty($filePath[2]) && file_exists($filePath[2]))&&(!empty($filePath[3]) && file_exists($filePath[3]))) {
				$transport = $this->_transportBuilder->setTemplateIdentifier('customform_form_template')
                ->setTemplateOptions($templateOptions)
                ->setTemplateVars($templateVars)
                ->setFrom($from)
                ->addTo($toemail)
				->setReplyTo($replyToEmail, $replyToName)
				->addAttachment(file_get_contents($filePath[0]), $fileName[0],$filetype[0])
				->addAttachment(file_get_contents($filePath[1]), $fileName[1],$filetype[1])
				->addAttachment(file_get_contents($filePath[2]), $fileName[2],$filetype[2])
				->addAttachment(file_get_contents($filePath[3]), $fileName[3],$filetype[3])
				->getTransport();
				$transport->sendMessage();
			}
			else if ((!empty($filePath[0]) && file_exists($filePath[0]))&&(!empty($filePath[1]) && file_exists($filePath[1]))&&(!empty($filePath[2]) && file_exists($filePath[2]))) {
				$transport = $this->_transportBuilder->setTemplateIdentifier('customform_form_template')
                ->setTemplateOptions($templateOptions)
                ->setTemplateVars($templateVars)
                ->setFrom($from)
                ->addTo($toemail)
				->setReplyTo($replyToEmail, $replyToName)
				->addAttachment(file_get_contents($filePath[0]), $fileName[0],$filetype[0])
				->addAttachment(file_get_contents($filePath[1]), $fileName[1],$filetype[1])
				->addAttachment(file_get_contents($filePath[2]), $fileName[2],$filetype[2])
				->getTransport();
				$transport->sendMessage();
			}
			else if ((!empty($filePath[0]) && file_exists($filePath[0]))&&(!empty($filePath[1]) && file_exists($filePath[1]))) {
				$transport = $this->_transportBuilder->setTemplateIdentifier('customform_form_template')
                ->setTemplateOptions($templateOptions)
                ->setTemplateVars($templateVars)
                ->setFrom($from)
                ->addTo($toemail)
				->setReplyTo($replyToEmail, $replyToName)
				->addAttachment(file_get_contents($filePath[0]), $fileName[0],$filetype[0])
				->addAttachment(file_get_contents($filePath[1]), $fileName[1],$filetype[1])
				->getTransport();
				$transport->sendMessage();
			}
			else if (!empty($filePath[0]) && file_exists($filePath[0])) {
				$transport = $this->_transportBuilder->setTemplateIdentifier('customform_form_template')
                ->setTemplateOptions($templateOptions)
                ->setTemplateVars($templateVars)
                ->setFrom($from)
                ->addTo($toemail)
				->setReplyTo($replyToEmail, $replyToName)
				->addAttachment(file_get_contents($filePath[0]), $fileName[0],$filetype[0])
				->getTransport();
				$transport->sendMessage();
			}
			else{
				$transport = $this->_transportBuilder->setTemplateIdentifier('customform_form_template')
                ->setTemplateOptions($templateOptions)
                ->setTemplateVars($templateVars)
                ->setFrom($from)
                ->addTo($toemail)
				->setReplyTo($replyToEmail, $replyToName)
				->getTransport();
				$transport->sendMessage();
			}
			
        
        $this->inlineTranslation->resume();

        $this->messageManager->addSuccess(__('Thanks for contacting us with your comments and questions. We\'ll respond to you very soon.'));

        $this->_redirect('contacts');
    }

}