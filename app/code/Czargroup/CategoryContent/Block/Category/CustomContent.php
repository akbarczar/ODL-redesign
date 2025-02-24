<?php
namespace Czargroup\CategoryContent\Block\Category;

use Magento\Catalog\Block\Category\View as CategoryView;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use Magento\Catalog\Helper\Category as CategoryHelper;

class CustomContent extends CategoryView
{
    /**
     * @var FilterProvider
     */
    protected $filterProvider;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param LayerResolver $layerResolver
     * @param \Magento\Framework\Registry $registry
     * @param FilterProvider $filterProvider
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        LayerResolver $layerResolver,
        \Magento\Framework\Registry $registry,
        FilterProvider $filterProvider,
        CategoryHelper $categoryHelper,
        array $data = []
    ) {
        $this->filterProvider = $filterProvider;
        parent::__construct($context, $layerResolver, $registry, $categoryHelper);
    }

    /**
     * Retrieve filtered content from the category attribute.
     *
     * @param string $attributeCode
     * @return string
     */
    public function getFilteredContent($attributeCode)
    {
        $category = $this->getCurrentCategory();
        $categorydata = $category->getData($attributeCode);
        if (!$categorydata) {
            return '';
        }
        return $this->filterProvider->getPageFilter()->filter($categorydata);
    }
}
