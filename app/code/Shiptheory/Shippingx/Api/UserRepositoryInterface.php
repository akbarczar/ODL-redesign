<?php
namespace Shiptheory\Shippingx\Api;

/**
 * @api
 */
interface UserRepositoryInterface
{
    /**
     * Get a list of admin users (id, name, email address).
     *
     * @return mixed[]
     */
    public function getList();

    /**
     * Returns a single user from ID
     *
     * @param $id
     * @return mixed
     */
    public function getById($id);
}
