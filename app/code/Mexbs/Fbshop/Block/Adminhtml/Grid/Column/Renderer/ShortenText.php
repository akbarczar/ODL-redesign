<?php
namespace Mexbs\Fbshop\Block\Adminhtml\Grid\Column\Renderer;

class ShortenText extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    protected function _getValue(\Magento\Framework\DataObject $row)
    {
        $data = parent::_getValue($row);
        if ($data !== null) {
            $value = (strlen($data) > 200 ? substr($data, 0, 200)." ..." : $data);
            return $value ? $value : '';
        }
        return $this->getColumn()->getDefault();
    }
}