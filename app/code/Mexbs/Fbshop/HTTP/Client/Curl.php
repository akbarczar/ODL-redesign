<?php
namespace Mexbs\Fbshop\HTTP\Client;

class Curl extends \Magento\Framework\HTTP\Client\Curl
{
    private $sslVersion;

    public function __construct($sslVersion = null)
    {
        $this->sslVersion = $sslVersion;
        parent::__construct($sslVersion);
    }

    public function delete($uri)
    {
        $this->makeRequest("DELETE", $uri);
    }
}