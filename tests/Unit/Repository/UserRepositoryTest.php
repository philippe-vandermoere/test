<?php

/**
 * @author      Philippe Vandermoere <vandermoere.philippe@gmail.com>
 * @copyright   (c) Philippe Vandermoere
 * @license     MIT
 */

namespace App\Tests\Unit\Repository;

use App\Entity\User;
use App\Repository\UserRepository;

class UserRepositoryTest extends AbstractRepository
{
    protected function getRepositoryClassName(): string
    {
        return UserRepository::class;
    }

    protected function getEntityClassName(): string
    {
        return User::class;
    }
}
