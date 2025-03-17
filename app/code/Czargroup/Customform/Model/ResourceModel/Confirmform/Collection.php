<?php
/**
 * Copyright (c) 2025 Czargroup Technologies. All rights reserved.
 *
 * @package Czargroup_Customform
 * @author Czargroup Technologies
 */
namespace Czargroup\Customform\Model\ResourceModel\Confirmform;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	protected $_idFieldName = 'id';
	protected $_eventPrefix = 'czargroup_customform_confirmform_collection';
	protected $_eventObject = 'confirmform_collection';

	/**
	 * Define resource model
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_init('Czargroup\Customform\Model\Confirmform', 'Czargroup\Customform\Model\ResourceModel\Confirmform');
	}

}
