<?php

namespace Shiptheory\Shippingx\Block\Adminhtml;

use Shiptheory\Shippingx\Block\Adminhtml\History\Grid;

class History extends \Magento\Backend\Block\Widget\Container
{
    protected $_template = 'history/history.phtml';

    protected function _prepareLayout()
    {
        $this->setChild(
            'grid',
            $this->getLayout()->createBlock(Grid::class, 'boostmyshop.history.grid')
        );
        return parent::_prepareLayout();
    }

    public function getGridHtml()
    {
        return $this->getChildHtml('grid');
    }
}
