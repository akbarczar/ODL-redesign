<?php
/**
 * Copyright © 2022 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\SimpleGoogleShopping\Controller\Adminhtml\Feeds;

/**
 * Generate sample action
 */
class Preview extends \Wyomind\SimpleGoogleShopping\Controller\Adminhtml\Feeds
{
    /**
     * Execute action
     */
    public function execute()
    {
        return $this->resultPageFactory->create();
    }
}
