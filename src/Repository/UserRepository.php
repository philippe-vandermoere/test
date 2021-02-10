<?php

/**
 * @author      Philippe Vandermoere <vandermoere.philippe@gmail.com>
 * @copyright   (c) Philippe Vandermoere
 * @license     MIT
 */

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use App\Exception\EntityNotFound;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 * @method User[] findAll()
 * @method User[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function find($id, $lockMode = null, $lockVersion = null): User
    {
        $user = parent::find($id, $lockMode, $lockVersion);

        if (false === ($user instanceof User)) {
            throw new EntityNotFound('Unable to find user.');
        }

        return $user;
    }

    public function findOneBy(array $criteria, array $orderBy = null): User
    {
        $user = parent::findOneBy($criteria, $orderBy);

        if (false === ($user instanceof User)) {
            throw new EntityNotFound('Unable to find user.');
        }

        return $user;
    }

    public function save(User $user): void
    {
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function delete(User $user): void
    {
        $this->getEntityManager()->remove($user);
        $this->getEntityManager()->flush();
    }
}
