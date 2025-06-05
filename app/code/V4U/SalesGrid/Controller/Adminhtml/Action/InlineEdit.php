<?php

namespace V4U\SalesGrid\Controller\Adminhtml\Action;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Sales\Api\OrderRepositoryInterface;

class InlineEdit extends Action
{
    protected $jsonFactory;
    protected $orderRepository;
    protected $model;

    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        OrderRepositoryInterface $orderRepository
    ) {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
        $this->orderRepository = $orderRepository; // 
    }

    public function execute()
    {
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/inline.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        $logger->info('start');

        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        if ($this->getRequest()->getParam('isAjax')) {
            $logger->info('if');
            $postItems = $this->getRequest()->getParam('items');
            $logger->info(print_r($postItems, true));
            if (empty($postItems)) {
                $logger->info('if 2');
                $messages[] = __('Please correct the data sent.');
                $error = true;
            } else {
                $logger->info('else');
                foreach ($postItems as $value) {
                    $logger->info('foreach');
                    $logger->info(print_r($value, true));
                    $logger->info($value['entity_id']);
                    try {
                        $logger->info('try');
                        $orderData = $this->orderRepository->get($value['entity_id']);
                        $orderData->setCustomField($value['custom_field']);
                        $orderData->save();
                    } catch (\Exception $e) {
                        $logger->info('catch');
                        $messages[] = "[Error:]  {$e->getMessage()}";
                        $error = true;
                    }
                }
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }
}
