<?php
/**
 * Public alias for the application entry point
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Framework\App\Bootstrap;

try {
    require __DIR__ . '/../app/bootstrap.php';
} catch (\Exception $e) {
    echo <<<HTML
<div style="font:12px/1.35em arial, helvetica, sans-serif;">
    <div style="margin:0 0 25px 0; border-bottom:1px solid #ccc;">
        <h3 style="margin:0;font-size:1.7em;font-weight:normal;text-transform:none;text-align:left;color:#2f2f2f;">
        Autoload error</h3>
    </div>
    <p>{$e->getMessage()}</p>
</div>
HTML;
    http_response_code(500);
    exit(1);
}
$params = $_SERVER;

if(isset($_SERVER['HTTP_HOST'])) {
    switch($_SERVER['HTTP_HOST']) {
        case 'odl.local':
        case 'www.odl.local':
            $params[\Magento\Store\Model\StoreManager::PARAM_RUN_CODE] = 'base'; // Website code as same in admin panel
        break;
        case 'wave.local':
        case 'www.wave.local':
            $params[\Magento\Store\Model\StoreManager::PARAM_RUN_CODE] = 'wwf'; // Website code as same in admin panel
        break;
    }
} else {
    // Fallback behavior when HTTP_HOST is not set
    $params[\Magento\Store\Model\StoreManager::PARAM_RUN_CODE] = 'default'; // Set a default run code
}
$params[\Magento\Store\Model\StoreManager::PARAM_RUN_TYPE] = 'website';
$bootstrap = Bootstrap::create(BP, $params);
/** @var \Magento\Framework\App\Http $app */
$app = $bootstrap->createApplication(\Magento\Framework\App\Http::class);
$bootstrap->run($app);
