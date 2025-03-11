<?php
/**
 * Copyright © Landofcoder.com All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Ves\Testimonial\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface TestimonialRepositoryInterface
{

    /**
     * Save Testimonial
     * @param \Ves\Testimonial\Api\Data\TestimonialInterface $post
     * @return \Ves\Testimonial\Api\Data\TestimonialInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Ves\Testimonial\Api\Data\TestimonialInterface $post
    );

    /**
     * Retrieve post
     * @param string $id
     * @return \Ves\Testimonial\Api\Data\TestimonialInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($id);

    /**
     * Retrieve Testimonial matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Ves\Testimonial\Api\Data\TestimonialSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Retrieve Testimonial matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Ves\Testimonial\Api\Data\TestimonialSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getPublishList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Testimonial
     * @param \Ves\Testimonial\Api\Data\TestimonialInterface $testimonial
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Ves\Testimonial\Api\Data\TestimonialInterface $testimonial
    );

    /**
     * Delete Testimonial by ID
     * @param string $testimonialId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($testimonialId);
}

