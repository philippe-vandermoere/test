<?php

/**
 * @author      Philippe Vandermoere <vandermoere.philippe@gmail.com>
 * @copyright   (c) Philippe Vandermoere
 * @license     MIT
 */

declare(strict_types=1);

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Component\Routing\Annotation\Route;

class UserDeleteController
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    #[Route(name: 'user_delete', path: '/users/{id}', methods: ['DELETE'])]
    public function __invoke(string $id): void
    {
        $this->userRepository->delete(
            $this->userRepository->findOneBy(['id' => $id])
        );
    }
}
