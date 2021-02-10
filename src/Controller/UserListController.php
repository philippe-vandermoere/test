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

class UserListController
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    /** @return User[] */
    #[Route(name: 'user_list', path: '/users', methods: ['GET'])]
    public function __invoke(): array
    {
        return $this->userRepository->findAll();
    }
}
