<?php
/**
 * Copyright (c) 2025 Czargroup Technologies. All rights reserved.
 *
 * @package Czargroup_MagentoImportExportExtend
 * @author Czargroup Technologies
 */

declare(strict_types = 1);

namespace Czargroup\MagentoImportExportExtend\Observer\Backend\Block\Widget\Grid\Extended;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Backend\Block\Widget\Grid;

class Button implements ObserverInterface
{
    /**
     * Apply select all button
     *
     * @param Observer $observer
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(Observer $observer)
    {
        /** @var 
         * \Magento\Backend\Block\Widget\Grid $grid 
         */
        $grid = $observer->getEvent()->getData('grid');
        if ($grid instanceof Grid && $grid->getId() == 'export_filter_grid') {
            $grid->setChild(
                'select_all_button',
                $grid->getLayout()->createBlock(
                    \Magento\Backend\Block\Widget\Button::class
                )->setData(
                    [
                        'label' => __('Select All'),
                        'onclick' => $grid->getJsObjectName() . '.selectAll()',
                        'class' => 'action-selectall select action-tertiary'
                    ]
                )->setDataAttribute(['action' => 'grid-select-all'])
            );
        }
    }
}