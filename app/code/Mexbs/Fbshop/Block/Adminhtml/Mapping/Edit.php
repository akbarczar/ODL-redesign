<?php
namespace Mexbs\Fbshop\Block\Adminhtml\Mapping;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    protected $_coreRegistry = null;

    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_mapping';
        $this->_blockGroup = 'Mexbs_Fbshop';

        parent::_construct();

        $this->buttonList->add(
            'save_and_continue_edit',
            [
                'class' => 'save',
                'label' => __('Save and Continue Edit'),
                'data_attribute' => [
                    'mage-init' => ['button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form']],
                ]
            ],
            10
        );
    }

    public function getHeaderText()
    {
        $rule = $this->_coreRegistry->registry('current_mapping');
        if ($rule->getRuleId()) {
            return __("Edit Mapping '%1'", $this->escapeHtml($rule->getName()));
        } else {
            return __('New Mapping');
        }
    }


    public function getBackUrl()
    {
        return $this->getUrl('fbshop/attributesmapping/index');
    }
}
