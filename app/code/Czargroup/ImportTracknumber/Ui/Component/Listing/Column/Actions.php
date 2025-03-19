<?php

/**
 * Copyright (c) 2025 Czargroup Technologies. All rights reserved.
 *
 * @package Czargroup_ImportTracknumber
 * @author  Czargroup
 */

namespace Czargroup\ImportTracknumber\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

/**
 * Class Actions
 *
 * Adds action links (Download, Delete) to the grid listing.
 */
class Actions extends Column
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * Actions constructor.
     *
     * @param ContextInterface $context UI component context.
     * @param UiComponentFactory $uiComponentFactory UI component factory.
     * @param UrlInterface $urlBuilder URL builder.
     * @param array $components UI component configuration.
     * @param array $data Additional data.
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepares the data source for the grid.
     *
     * @param array $dataSource The data source array.
     * @return array Modified data source with action links.
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $name = $this->getData('name');
                $item[$name]['download'] = [
                    'href' => $this->urlBuilder->getUrl(
                        'importtracknumber/orderindex/download',
                        ['id' => $item['id']]
                    ),
                    'label' => __('Download')
                ];
                $item[$name]['delete'] = [
                    'href' => $this->urlBuilder->getUrl(
                        'importtracknumber/orderindex/delete',
                        ['id' => $item['id']]
                    ),
                    'label' => __('Delete'),
                    'confirm' => [
                        'title' => __('Delete %1', $item['file_name']),
                        'message' => __('Are you sure you want to delete %1?', $item['file_name'])
                    ]
                ];
            }
        }
        return $dataSource;
    }
}
