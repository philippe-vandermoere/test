<?php

/**
 * @author      Philippe Vandermoere <vandermoere.philippe@gmail.com>
 * @copyright   (c) Philippe Vandermoere
 * @license     MIT
 */

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Routing\Annotation\Route;

class UserGetController
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    #[Route(name: 'user_get', path: '/users/{id}', methods: ['GET'])]
    public function __invoke(string $id): User
    {
        return $this->userRepository->findOneBy(['id' => $id]);
    }
}
