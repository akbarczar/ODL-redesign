<?php

namespace Shiptheory\Shippingx\Model\Api;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\User\Model\ResourceModel\User\Collection;
use Magento\User\Model\UserFactory;
use Shiptheory\Shippingx\Api\UserRepositoryInterface;

/**
 * Class UserRepository
 * @package Shiptheory\Shippingx\Model\Api
 */
class UserRepository implements UserRepositoryInterface
{
    /**
     * @var Collection
     */
    private Collection $userCollection;

    /**
     * @var UserFactory
     */
    private UserFactory $userFactory;

    /**
     * UserRepository constructor.
     * @param Collection $userCollection
     */
    public function __construct(
        Collection $userCollection,
        UserFactory $userFactory
    )
    {
        $this->userCollection = $userCollection;
        $this->userFactory = $userFactory;
    }

    /**
     * Get return type by type name and method name.
     *
     * @return mixed[]
     */
    public function getList()
    {
        $users = [];

        foreach ($this->userCollection->getItems() as $user) {
            $users[] = [
                'id'    => $user->getId(),
                'name'    => $user->getName(),
                'email'    => $user->getEmail()
            ];
        }

        return $users;
    }

    /**
     * @param $id
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getById($id)
    {
        $user = $this->userFactory->create();
        $user->getResource()->load($user, $id);
        if (!$user->getId()) {
            throw new NoSuchEntityException(__('User with ID "%1" does not exist.', $id));
        }

        return $user;
    }
}
