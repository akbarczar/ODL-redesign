<?php
namespace Mexbs\Fbshop\Block\Adminhtml\Mapping\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Backend\Block\Widget\Context;


class SaveButton implements ButtonProviderInterface
{
    protected $context;

    /**
     * @var BlockRepositoryInterface
     */
    protected $blockRepository;

    /**
     * @param Context $context
     * @param BlockRepositoryInterface $blockRepository
     */
    public function __construct(
        Context $context,
        BlockRepositoryInterface $blockRepository
    ) {
        $this->context = $context;
        $this->blockRepository = $blockRepository;
    }

    /**
     * Return CMS block ID
     *
     * @return int|null
     */
    public function getBlockId()
    {
        try {
            return $this->blockRepository->getById(
                $this->context->getRequest()->getParam('block_id')
            )->getId();
        } catch (NoSuchEntityException $e) {
        }
        return null;
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }

    public function getButtonData()
    {
        return [
            'label' => __('Save Mapping'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
                'on_click' => ''
            ],
            'sort_order' => 90,
        ];
    }
}
