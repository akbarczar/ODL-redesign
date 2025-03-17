<?php
namespace Mexbs\Fbshop\Model\Config\Source;

use Magento\Customer\Api\Data\GroupInterface;

class CustomerGroup extends \Magento\Customer\Model\Customer\Source\Group
{
    const DAILY = 'DAILY';
    const HOURLY = 'HOURLY';

    public function toOptionArray()
    {
        $customerGroups = parent::toOptionArray();

        $customerGroupsWithoutAll = [];
        foreach ($customerGroups as $customerGroup){
            if($customerGroup['value'] != (string)GroupInterface::CUST_GROUP_ALL){
                $customerGroupsWithoutAll[] = $customerGroup;
            }
        }

        return $customerGroupsWithoutAll;
    }
}
