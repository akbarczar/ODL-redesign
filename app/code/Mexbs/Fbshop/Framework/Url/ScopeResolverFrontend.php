<?php
namespace Mexbs\Fbshop\Framework\Url;

class ScopeResolverFrontend extends \Magento\Framework\Url\ScopeResolver
{
    public function __construct(
        \Magento\Framework\App\ScopeResolverInterface $scopeResolver
    )
    {
        parent::__construct(
            $scopeResolver,
            "frontend"
        );
    }
}