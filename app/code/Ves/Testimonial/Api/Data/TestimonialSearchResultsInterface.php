<?php


namespace Ves\Testimonial\Api\Data;

interface TestimonialSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Testimonial list.
     * @return \Ves\Testimonial\Api\Data\TestimonialInterface[]
     */
    public function getItems();

    /**
     * Set Testimonial list.
     * @param \Ves\Testimonial\Api\Data\TestimonialInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
