<?php
/**
 * Copyright (c) 2025 Czargroup Technologies. All rights reserved.
 *
 * @package Czargroup_Customform
 * @author Czargroup Technologies
 */
namespace Czargroup\Customform\Model;
class Confirmform extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
	const CACHE_TAG = 'czargroup_customform_confirmform';

	protected $_cacheTag = 'czargroup_customform_confirmform';

	protected $_eventPrefix = 'czargroup_customform_confirmform';

	protected function _construct()
	{
		$this->_init('Czargroup\Customform\Model\ResourceModel\Confirmform');
	}

	public function getIdentities()
	{
		return [self::CACHE_TAG . '_' . $this->getId()];
	}

	public function getDefaultValues()
	{
		$values = [];

		return $values;
	}
}