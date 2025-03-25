<?php
namespace Shiptheory\Shippingx\Api;

interface ApiInterface
{
    /**
     * @param $websiteId
     * @return mixed
     */
    public function createIntegration($websiteId);

    /**
     * @return mixed
     */
    public function removeIntegration();
}
