<?php
namespace Mexbs\Fbshop\Block\Adminhtml\CustomOptionsMapping\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class DeleteButton implements ButtonProviderInterface
{
    protected $context;

    public function __construct(
        \Magento\Backend\Block\Widget\Context $context
    ) {
        $this->context = $context;
    }


    protected function getMappingId(){
        return $this->context->getRequest()->getParam('id');
    }

    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }


    public function getButtonData()
    {
        $data = [];
        $mappingId = $this->getMappingId();
        if ($mappingId) {
            $data = [
                'label' => __('Delete Mapping'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . __(
                    'Are you sure you want to do this?'
                ) . '\', \'' . $this->getDeleteUrl() . '\')',
                'sort_order' => 20,
            ];
        }
        return $data;
    }

    /**
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', ['id' => $this->getMappingId()]);
    }
}
