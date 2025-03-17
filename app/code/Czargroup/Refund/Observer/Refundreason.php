<?php

namespace Czargroup\Refund\Observer;

use Magento\Framework\App\RequestInterface;
use Magento\Sales\Model\Order\Creditmemo\CommentFactory;
use Magento\Sales\Api\CreditmemoCommentRepositoryInterface;

class Refundreason implements \Magento\Framework\Event\ObserverInterface
{
	protected $_request;
	protected $creditMemoCommentFactory;
    protected $creditmemoRepository;
	protected $collectionFactory;

	public function __construct(
		RequestInterface $_request,
		CommentFactory $creditMemoCommentFactory,
		\Magento\Sales\Model\ResourceModel\Order\Creditmemo\Comment\CollectionFactory $collectionFactory,
        CreditmemoCommentRepositoryInterface $creditmemoRepository
	) {
		$this->_request = $_request;
		$this->creditMemoCommentFactory = $creditMemoCommentFactory;
		$this->collectionFactory = $collectionFactory;
        $this->creditmemoRepository = $creditmemoRepository;
	}
	
	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		
		$data = $this->_request->getParams();
        
		
        $creditmemo = $observer->getEvent()->getCreditmemo();
        if (!empty($data['creditmemo']['refund_reasons'])) {
			
            
            $creditmemo->setRefundReasons($data['creditmemo']['refund_reasons']);
            
        }		 

        return $this; 

	 }
}