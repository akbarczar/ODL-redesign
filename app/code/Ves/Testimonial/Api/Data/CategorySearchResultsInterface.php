<?php


namespace Ves\Testimonial\Api\Data;

interface CategorySearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get category list.
     * @return \Ves\Testimonial\Api\Data\CategoryInterface[]
     */
    public function getItems();

    /**
     * Set category list.
     * @param \Ves\Testimonial\Api\Data\CategoryInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
