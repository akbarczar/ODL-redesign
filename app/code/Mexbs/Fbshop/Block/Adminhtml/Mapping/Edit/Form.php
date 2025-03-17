<?php
namespace Mexbs\Fbshop\Block\Adminhtml\Mapping\Edit;


class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('adminhtml_mapping_form');
        $this->setTitle(__('Mapping Information'));
    }

    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getUrl('fbshop/attributesmapping/save'),
                    'method' => 'post',
                ],
            ]
        );
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
