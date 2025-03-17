<?php
namespace Mexbs\Fbshop\Framework;

use \Magento\Framework\FlagFactory;
use \Magento\Framework\Flag\FlagResource;

class FlagManager
{
    private $flagFactory;
    private $flagResource;

    public function __construct(
        FlagFactory $flagFactory,
        FlagResource $flagResource
    ) {
        $this->flagFactory = $flagFactory;
        $this->flagResource = $flagResource;
    }

    private function getFlagObject($code)
    {
        /** @var \Magento\Framework\Flag $flag */
        $flag = $this->flagFactory->create(['data' => ['flag_code' => $code]]);
        $this->flagResource->load(
            $flag,
            $code,
            'flag_code'
        );

        return $flag;
    }

    public function getFlagLastUpdate($code)
    {
        return $this->getFlagObject($code)->getLastUpdate();
    }

    public function getFlagData($code)
    {
        return $this->getFlagObject($code)->getFlagData();
    }

    public function saveFlag($code, $value)
    {
        $flag = $this->getFlagObject($code);
        $flag->setFlagData($value);
        $this->flagResource->save($flag);

        return true;
    }

    public function deleteFlag($code)
    {
        $flag = $this->getFlagObject($code);

        if ($flag->getId()) {
            $this->flagResource->delete($flag);
        }

        return true;
    }
}